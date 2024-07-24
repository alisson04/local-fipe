<?php

namespace Src;

use Src\FipeApi;

class ResponseProvider
{
    const URI_REFERENCE_TABLES = 'ConsultarTabelaDeReferencia';
    const URI_BRANDS = 'ConsultarMarcas';
    const URI_MODELS = 'ConsultarModelos';
    const URI_YEAR_MODEL = 'ConsultarAnoModelo';
    const URI_MODELS_BY_YEAR = 'ConsultarModelosAtravesDoAno';
    const URI_PRICE = 'ConsultarValorComTodosParametros';
    
    public function run(string $route, array $formParams = []): array
    {
        $array = [
            self::URI_REFERENCE_TABLES => 'responses/references/',
            self::URI_BRANDS => 'responses/brands/',
        ];

        $jsonFilePath = $array[$route] . $this->generateFileName($formParams);;

        if (file_exists($jsonFilePath)) {
            return $this->getDataFromJsonFile($jsonFilePath);
        }

        return $this->getDataFromFipeApi($route, $formParams, $jsonFilePath);
    }

    private function generateFileName(array $formParams): string
    {
        if (empty($formParams)) {
            return 'references.json';
        }

        $fileName = '';

        foreach ($formParams as $key => $value) {
            $fileName .= strtolower("_{$key}_{$value}");
        }

        return substr($fileName, 1) . '.json';
    }

    private function saveDataInJsonFile(string $content, string $jsonFilePath): void
    {
        $jsonFilePath = explode('/', $jsonFilePath);
        $fileName = array_pop($jsonFilePath);
        $jsonFilePath = implode('/', $jsonFilePath);

        $this->createPathIfNecessary($jsonFilePath);

        if (file_put_contents("{$jsonFilePath}/{$fileName}", $content)) {
            echo "A resposta foi salva em $jsonFilePath\n";
        } else {
            echo "Falha ao salvar a resposta em $jsonFilePath\n";
        }
    }

    private function getDataFromFipeApi(string $route, array $formParams, string $jsonFilePath): array
    {
        $fipeApi = new FipeApi();
        $content = $fipeApi->post($route, $formParams);
        $this->saveDataInJsonFile($content, $jsonFilePath);
        
        return json_decode($content, true);
    }

    private function getDataFromJsonFile(string $jsonFilePath): array
    {
        $jsonContents = file_get_contents($jsonFilePath);
        return $this->getDataFromJson($jsonContents);
    }

    private function getDataFromJson(string $json): array
    {
        $data = json_decode($json, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            echo "JSON data successfully decoded.\n";
        } else {
            echo "Error decoding JSON data: " . json_last_error_msg() . "\n";
        }

        return $data;
    }

    private function createPathIfNecessary(string $path): void
    {
        if (! is_dir($path)) {
            mkdir($path, 0700, true);
        }
    }
}