<?php

namespace Src;

use Src\FipeApi;
use Src\ResponseJsonFileManager;

class ResponseProvider
{
    private $fipeApi;
    private $responseJsonFileManager;

    public function __construct() {
        $this->fipeApi = new FipeApi(0,0);
        $this->responseJsonFileManager = new ResponseJsonFileManager();
    }
    
    public function run(string $route, array $formParams = []): array
    {
        $jsonFilePath = $this->responseJsonFileManager->generateFilePath($route, $formParams);

        if (file_exists($jsonFilePath)) {
            echo "File already exists: {$jsonFilePath} \n";
            return $this->responseJsonFileManager->getDataFromJsonFile($jsonFilePath);
        }

        echo "File don't exists: {$jsonFilePath} \n";
        return $this->getDataFromFipeApi($route, $formParams, $jsonFilePath);
    }

    private function getDataFromFipeApi(string $route, array $formParams, string $jsonFilePath): array
    {
        $content = $this->fipeApi->post($route, $formParams);
        $this->responseJsonFileManager->saveDataInJsonFile($content, $jsonFilePath);
        
        return json_decode($content, true);
    }
}