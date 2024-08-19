<?php

namespace Src;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://veiculos.fipe.org.br/api/veiculos/']);
        $this->setProxiesIfExist();
    }

    public function post(string $uri, array $formParams = []): string
    {
        sleep($this->sleepTimeRequest);

        try {
            $body = ['headers' => ['Content-Type' => 'application/json'], 'json' => $formParams];
            $body = $this->setProxyInBody($body);
            $response = $this->client->request('POST', $uri, $body);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() === 429) {
                $this->increaseSleepTimeRequest();
            }

            throw $e;
        }

        $content = $response->getBody()->getContents();
        $data = $this->getDataFromJson($content);

        return $content;
    }

    private function getDataFromJson(string $json): array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding JSON data: " . json_last_error_msg() . "\n";
            die();
        }
        
        if (isset($data['erro']) && $data['erro'] === 'Parâmetros inválidos') {
            print_r($data);
            print_r($uri);
            print_r($formParams);
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
