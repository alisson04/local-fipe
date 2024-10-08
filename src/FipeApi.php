<?php

namespace Src;

use GuzzleHttp\Client;
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

    private function gerenateFileResponsePath(string $uri, array $formParams): string
    {
        $mapUris = [
            'ConsultarTabelaDeReferencia' => 'references',
            'ConsultarMarcas' => 'brands',
            'ConsultarModelos' => 'models',
            'ConsultarAnoModelo' => 'year-model',
            'ConsultarValorComTodosParametros' => 'prices',
        ];

        if (! isset($mapUris[$uri])) {
            throw new \Exception("Uri not found: {$uri}");
        }

        $mapParams = [
            'codigoTabelaReferencia' => 'reference',
            'codigoTipoVeiculo' => 'vehicle-type',
        ];

        foreach ($formParams as $keyParam => $value) {
            if (! isset($mapParams[$keyParam])) {
                throw new \Exception("Param not found: {$keyParam}");
            }

            if (! $mapParams[$keyParam]) {
                unset($formParams[$keyParam]);
                continue;
            }

            $keyMapped = $mapParams[$keyParam];
            $formParams[$keyMapped] = $value;
        }

        return $this->responseJsonFileManager->generateFilePath($mapUris[$uri], $formParams);
    }

    public function poolPost(array $requests): void
    {
        $paths = [];
        foreach ($requests as $index => $request) {
            if ($request['uri'] === 'ConsultarTabelaDeReferencia') {
                throw new \Exception("Pool should not be used for uri: {$uri}");
            }

            $jsonFilePath = $this->gerenateFileResponsePath($request['uri'], $request['params']);
            if (file_exists($jsonFilePath) && filesize($jsonFilePath) > 0) {
                unset($requests[$index]);
            } else {
                $paths[] = $jsonFilePath;
            }
        }

        if (empty($requests)) {
            return;
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

        $requestsSuccess = 0;
        $requestsFailed = 0;
        $pool = new Pool($this->client, $requestFunction($requests), [
            'concurrency' => 100,
            'fulfilled' => function (Response $response, $index) use($paths, &$requestsSuccess) {
                $requestsSuccess++;
                $jsonFilePath = $paths[$index];
                $this->saveDataInJsonFile($response, $jsonFilePath);
            },
            'rejected' => function (TransferException $reason, $index) use(&$requestsFailed) {
                $requestsFailed++;

                if ($reason->getCode() == 502 || $reason->getCode() == 429) {
                    sleep(5);
                }
            },
        ]);

        $promise = $pool->promise();
        $promise->wait();

        $this->poolPost($requests);
    }

    public function post(string $uri, array $formParams = []): array
    {
        $jsonFilePath = $this->gerenateFileResponsePath($uri, $formParams);

        if ($uri !== 'ConsultarTabelaDeReferencia' && file_exists($jsonFilePath) && filesize($jsonFilePath) > 0) {
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
