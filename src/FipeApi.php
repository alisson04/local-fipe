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
    private array $defaultFormParams;
    private int $sleepTimeRequest = 1;

    public function __construct()
    {
        $this->defaultFormParams = [];
        $this->client = new Client(['base_uri' => 'https://veiculos.fipe.org.br/api/veiculos/']);
    }

    public function post(string $uri, array $formParams = []): string
    {
        sleep($this->sleepTimeRequest);

        try {
            $body = ['headers' => ['Content-Type' => 'application/json'], 'json' => $formParams];
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

    public function setReference(int $referenceId): void
    {
        $this->defaultFormParams['codigoTabelaReferencia'] = $referenceId;
    }

    public function setVehicleTypeMotorcycle(): void
    {
        $this->defaultFormParams['codigoTipoVeiculo'] = self::VEHICLE_TYPE_MOTORCYCLE;
    }

    public function setVehicleTypeCar(): void
    {
        $this->defaultFormParams['codigoTipoVeiculo'] = self::VEHICLE_TYPE_CAR;
    }

    public function setVehicleTypeTruck(): void
    {
        $this->defaultFormParams['codigoTipoVeiculo'] = self::VEHICLE_TYPE_TRUCK;
    }
}