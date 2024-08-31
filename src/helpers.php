<?php
// GENERAL HELPERS
// ========================================================
if (!function_exists('dd')) {
    function dd(...$args)
    {
        foreach ($args as $arg) {
            var_dump($arg);
        }
        die();
    }
}

// FILE HELPERS
// ========================================================
if (!function_exists('createPathIfDontExist')) {
    function createPathIfDontExist(string $path): void
    {
        if (! is_dir($path)) {
            mkdir($path, 0700, true);
        }
    }
}

if (!function_exists('saveArrayInJsonFile')) {
    function saveArrayInJsonFile(array $arrayData, string $filePath, string $fileName): void
    {
        $jsonContent = json_encode($arrayData);
        saveJsonInFile($jsonContent, $filePath, $fileName);
    }
}

if (!function_exists('saveDataInJsonFile')) {
    function saveJsonInFile(string $jsonContent, string $filePath, string $fileName): void
    {
        createPathIfDontExist($filePath);

        if (! file_put_contents("{$filePath}/{$fileName}", $jsonContent)) {
            echo "Falha ao salvar a resposta em $jsonFilePath\n";
            die();
        }
    }
}

if (!function_exists('getJsonLikeArrayFromFile')) {
    function getJsonLikeArrayFromFile(string $jsonFilePath): array
    {
        $jsonContents = file_get_contents($jsonFilePath);
        return getDataFromJson($jsonContents);
    }
}

if (!function_exists('getDataFromJson')) {
    function getDataFromJson(string $json): array
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Error decoding JSON data: ' . json_last_error_msg());
        }

        return $data;
    }
}