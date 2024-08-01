<?php

namespace Src;

use Src\FipeApi;
use GuzzleHttp\Exception\ConnectException;

class ResponseCascadeUpdater
{
    private $responseProvider;

    public function __construct() {
        $this->responseProvider = new ResponseProvider();
    }

    public function run(): void
    {
        $vehicleTypes = [FipeApi::VEHICLE_TYPE_CAR, FipeApi::VEHICLE_TYPE_MOTORCYCLE, FipeApi::VEHICLE_TYPE_TRUCK];
        $references = $this->responseProvider->run('ConsultarTabelaDeReferencia');

        try {
            foreach ($references as $reference) {
                $referenceId = $reference['Codigo'];

                foreach ($vehicleTypes as $vehicleType) {
                    $params = ['codigoTabelaReferencia' => $referenceId, 'codigoTipoVeiculo' => $vehicleType];
                    $brands = $this->responseProvider->run('ConsultarMarcas', $params);

                    foreach ($brands as $brand) {
                        $brandId = $brand['Value'];

                        $params['codigoMarca'] = $brandId;
                        $brands = $this->responseProvider->run('ConsultarModelos', $params);
                    }
                }
            }
        } catch (\ConnectException $e) {
            echo $e->getMessage() . "\n";
            echo "Retrying in 5 seconds...\n";
            sleep(5);
            $this->run();
        }
    }
}
