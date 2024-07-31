<?php

namespace Src;

use Src\FipeApi;

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

        foreach ($references as $reference) {
            foreach ($vehicleTypes as $vehicleType) {
                $params = ['codigoTabelaReferencia' => $reference['Codigo'], 'codigoTipoVeiculo' => $vehicleType];
                $this->responseProvider->run('ConsultarMarcas', $params);
            }
        }
    }
}
