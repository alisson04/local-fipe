<?php

namespace Src;

use Src\FipeApi;
use Src\ResponseJsonFileManager;

class ResponseProvider
{
    const URI_REFERENCE_TABLES = 'ConsultarTabelaDeReferencia';
    const URI_BRANDS = 'ConsultarMarcas';
    const URI_MODELS = 'ConsultarModelos';
    const URI_YEAR_MODEL = 'ConsultarAnoModelo';
    const URI_MODELS_BY_YEAR = 'ConsultarModelosAtravesDoAno';
    const URI_PRICE = 'ConsultarValorComTodosParametros';

    private $fipeApi;
    private $responseJsonFileManager;

    public function __construct() {
        $this->fipeApi = new FipeApi();
        $this->responseJsonFileManager = new ResponseJsonFileManager();
    }
    
    public function run(string $route, array $formParams = []): array
    {
        $array = [
            self::URI_REFERENCE_TABLES => 'responses/references/',
            self::URI_BRANDS => 'responses/brands/',
        ];

        $jsonFilePath = $array[$route] . $this->responseJsonFileManager->generateFileName($formParams);;

        if (file_exists($jsonFilePath)) {
            return $this->responseJsonFileManager->getDataFromJsonFile($jsonFilePath);
        }

        return $this->getDataFromFipeApi($route, $formParams, $jsonFilePath);
    }

    private function getDataFromFipeApi(string $route, array $formParams, string $jsonFilePath): array
    {
        $content = $this->fipeApi->post($route, $formParams);
        $this->responseJsonFileManager->saveDataInJsonFile($content, $jsonFilePath);
        
        return json_decode($content, true);
    }
}