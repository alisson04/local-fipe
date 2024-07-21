<?php

namespace Src;

use Src\FipeApi;

class ResponseProvider
{
    public function run(string $route): array
    {
        $array = [
            'ConsultarTabelaDeReferencia' => 'responses/references/references.json',
        ];

        $jsonFilePath = $array[$route];

        if (file_exists($jsonFilePath)) {
            return $this->getDataFromJsonFile();
        }

        return $this->getDataFromFipeApi();        
    }

    private function saveDataInJsonFile(array $content, string $jsonFilePath): void
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

    private function getDataFromFipeApi(string $jsonFilePath): array
    {
        $fipeApi = new FipeApi();
        $content = $fipeApi->getReferenceTables();
    }

    private function getDataFromJsonFile(string $jsonFilePath): array
    {
        $jsonContents = file_get_contents($jsonFilePath);

        $data = json_decode($jsonContents, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            echo "JSON data successfully decoded.\n";
        } else {
            // Error in decoding JSON data
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