<?php

namespace Src;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;

class FipeApi
{
    const URI_BASE = 'https://veiculos.fipe.org.br/api/veiculos/';
    const URI_REFERENCE_TABLES = 'ConsultarTabelaDeReferencia';
    const URI_BRANDS = 'ConsultarMarcas';
    const URI_MODELS = 'ConsultarModelos';
    const URI_YEAR_MODEL = 'ConsultarAnoModelo';
    const URI_MODELS_BY_YEAR = 'ConsultarModelosAtravesDoAno';
    const URI_PRICE = 'ConsultarValorComTodosParametros';

    const VEHICLE_TYPE_CAR = 1;
    const VEHICLE_TYPE_MOTORCYCLE = 2;
    const VEHICLE_TYPE_TRUCK = 3;

    private Client $client;
    private array $defaultFormParams = [];
    private int $sleepTimeRequest = 1;
    private array $proxies = [];
    private int $proxyIndex = 0;

    private $responseJsonFileManager;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://veiculos.fipe.org.br/api/veiculos/']);
        $this->responseJsonFileManager = new ResponseJsonFileManager();
        $this->setProxiesIfExist();
    }

    public function poolPost(array $requests): void
    {
        echo "Initializing pool... " . count($requests) . " requests" . PHP_EOL;
        $paths = [];
        foreach ($requests as $index => $request) {
            $jsonFilePath = $this->responseJsonFileManager->generateFilePath($request['uri'], $request['params']);
            if (file_exists($jsonFilePath)) {
                unset($requests[$index]);
            } else {
                $paths[] = $jsonFilePath;
            }
        }

        if (empty($requests)) {
            dd('End');
        }

        $requestFunction = function ($requests) {
            foreach ($requests as $request) {
                $body = ['json' => $request['params']];
                $body = $this->setProxyInBody($body);
                yield function() use ($request, $body) {
                    return $this->client->postAsync($request['uri'], $body);
                };
            }
        };

        $pool = new Pool($this->client, $requestFunction($requests), [
            'concurrency' => 10,
            'fulfilled' => function (Response $response, $index) use($paths) {
                $jsonFilePath = $paths[$index];
                echo "{$index} Success: {$jsonFilePath}" . PHP_EOL;
                $this->saveDataInJsonFile($response, $jsonFilePath);
            },
            'rejected' => function (TransferException $reason, $index) {
                if ($reason instanceof ConnectException) {
                    echo $index . ' failed due to connection issue: ' . $reason->getMessage() . PHP_EOL;
                } elseif ($reason instanceof RequestException) {
                    echo $index . ' failed due to request issue: ' . $reason->getMessage() . PHP_EOL;
                } else {
                    echo $index . ' failed due to an unexpected issue: ' . $reason->getMessage() . PHP_EOL;
                }

                if ($reason->getCode() == 502) {
                    // Tente novamente após um breve intervalo
                    sleep(2);
                    echo $index . ' retrying due to 502 error' . PHP_EOL;
                    // Reenviar a requisição ou adicionar lógica de retry
                }
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        $this->poolPost($requests);
    }

    public function post(string $uri, array $formParams = []): array
    {
        $jsonFilePath = $this->responseJsonFileManager->generateFilePath($uri, $formParams);

        if ($uri !== 'ConsultarTabelaDeReferencia' && file_exists($jsonFilePath)) {
            return $this->responseJsonFileManager->getDataFromJsonFile($jsonFilePath);
        }

        try {
            $body = ['json' => $formParams];
            $body = $this->setProxyInBody($body);
            $response = $this->client->request('POST', $uri, $body);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 429) {
                $this->increaseSleepTimeRequest();
            }

            throw new \Exception($e->getMessage());
        }

        return $this->saveDataInJsonFile($response, $jsonFilePath);
    }

    private function saveDataInJsonFile($response, $jsonFilePath): array
    {
        $content = $response->getBody()->getContents();
        $data = $this->checkErrosAndGetDataFromJson($content);
        $this->responseJsonFileManager->saveDataInJsonFile($content, $jsonFilePath);

        return $data;
    }

    private function checkErrosAndGetDataFromJson(string $json): array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding JSON data: " . json_last_error_msg() . "\n";
            die();
        }
        
        if (isset($data['erro']) && $data['erro'] === 'Parâmetros inválidos') {
            print_r($data);
            die();
        }

        if (isset($data['erro'])) {
            throw new \Exception($data['erro']);
            die();
        }

        return $data;
    }

    private function increaseSleepTimeRequest(): void
    {
        $this->sleepTimeRequest += 1;
        echo "Sleeping time increased to {$this->sleepTimeRequest} \n";
    }

    private function setProxiesIfExist(): void
    {
        $proxiesPath = 'config/proxies.json';
        if (file_exists($proxiesPath)) {
            $this->proxies = json_decode(file_get_contents($proxiesPath), true);
            $this->proxies = $this->proxies['proxies'];
            $this->sleepTimeRequest = 0;
        }
    }

    private function setProxyInBody(array $body): array
    {
        if (count($this->proxies) > 0) {
            $proxy = $this->proxies[$this->proxyIndex];
            $body['proxy'] = $proxy;

            $this->proxyIndex++;

            if ($this->proxyIndex >= count($this->proxies)) {
                $this->proxyIndex = 0;
            }
        }

        return $body;
    }
}
