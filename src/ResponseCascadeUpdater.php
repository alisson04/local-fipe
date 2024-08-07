<?php

namespace Src;

use Src\FipeApi;

class ResponseCascadeUpdater
{
    private $responseProvider;
    private array $requestParams;

    public function __construct() {
        $this->responseProvider = new ResponseProvider();
        $this->requestParams = [];
    }

    public function run(): void
    {
        $vehicleTypes = [FipeApi::VEHICLE_TYPE_CAR, FipeApi::VEHICLE_TYPE_MOTORCYCLE, FipeApi::VEHICLE_TYPE_TRUCK];
        $references = $this->responseProvider->run('ConsultarTabelaDeReferencia');

        try {
            foreach ($references as $reference) {
                $this->requestParams['codigoTabelaReferencia'] = $reference['Codigo'];

                foreach ($vehicleTypes as $vehicleType) {
                    $this->requestParams['codigoTipoVeiculo'] = $vehicleType;
                    $this->updateBrands();
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
            echo "Retrying in 2 seconds...\n";
            sleep(2);
            $this->run();
        }

        echo "Done!\n";
    }

    private function updateBrands(): void
    {
        $brands = $this->responseProvider->run('ConsultarMarcas', $this->requestParams);

        foreach ($brands as $brand) {
            $this->requestParams['codigoMarca'] = $brand['Value'];
            $this->updateModels();
        }
    }

    private function updateModels(): void
    {        
        $models = $this->responseProvider->run('ConsultarModelos', $this->requestParams);
        
        foreach ($models['Modelos'] as $model) {
            $this->requestParams['codigoModelo'] = $model['Value'];
            $this->updateYearModels();
        }
    }

    private function updateYearModels(): void
    {
        $this->responseProvider->run('ConsultarAnoModelo', $this->requestParams);
    }
}
