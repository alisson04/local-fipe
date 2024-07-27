<?php

namespace Src;

use GuzzleHttp\Client;

class FipeApi
{
    const URI_BASE = 'https://veiculos.fipe.org.br/api/veiculos/';
    const URI_REFERENCE_TABLES = 'ConsultarTabelaDeReferencia';
    const URI_BRANDS = 'ConsultarMarcas';
    const URI_MODELS = 'ConsultarModelos';
    const URI_YEAR_MODEL = 'ConsultarAnoModelo';
    const URI_MODELS_BY_YEAR = 'ConsultarModelosAtravesDoAno';
    const URI_PRICE = 'ConsultarValorComTodosParametros';

    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => 'https://veiculos.fipe.org.br/api/veiculos/']);
    }

    public function post(string $uri, array $formParams = []): string
    {
        sleep(2);
        $body = ['headers' => ['Content-Type' => 'application/json'], 'json' => $formParams];
        $response = $this->client->request('POST', $uri, $body);

        return $response->getBody()->getContents();
    }
}