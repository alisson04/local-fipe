<?php

namespace Src;

use Src\FipeApi;

class ResponseCascadeManager
{
    private $responseProvider;

    public function __construct() {
        $this->responseProvider = new ResponseProvider();
    }
    
    public function run(string $route, array $formParams = []): array
    {
        
    }
}