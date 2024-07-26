<?php

namespace Src;

class ResponseJsonFileManager
{
    public function generateFileName(array $formParams): string
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

    public function saveDataInJsonFile(string $content, string $jsonFilePath): void
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

    public function getDataFromJsonFile(string $jsonFilePath): array
    {
        $jsonContents = file_get_contents($jsonFilePath);
        return $this->getDataFromJson($jsonContents);
    }

    public function getDataFromJson(string $json): array
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