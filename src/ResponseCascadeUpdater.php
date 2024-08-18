<?php

namespace Src;

use Src\FipeApi;
use Src\ProgressBar;

class ResponseCascadeUpdater
{
    private $progressBar;
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
            $this->progressBar = new ProgressBar(count($references));

            foreach ($references as $key => $reference) {
                $this->requestParams['codigoTabelaReferencia'] = $reference['Codigo'];

                foreach ($vehicleTypes as $vehicleType) {
                    $this->progressBar->setCurrentMessage("Downloading: {$reference['Mes']} -> {$vehicleType}");
                    $this->requestParams['codigoTipoVeiculo'] = $vehicleType;
                    $this->updateBrands();
                }

                $this->progressBar->setCurrentValue($key + 1);
            }
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n";
            echo "Retrying in 2 seconds...\n";
            sleep(2);
            $this->run();
        }

        echo "Done!!!\n";
    }

    private function updateBrands(): void
    {
        $brands = $this->responseProvider->run('ConsultarMarcas', $this->requestParams);
        $currentMessage = $this->progressBar->getCurrentMessage();

        foreach ($brands as $brand) {
            $this->progressBar->setCurrentMessage("{$currentMessage} -> {$brand['Label']}");
            $this->requestParams['codigoMarca'] = $brand['Value'];
            $this->updateModels();
        }
    }

    private function updateModels(): void
    {
        $models = $this->responseProvider->run('ConsultarModelos', $this->requestParams);
        $currentMessage = $this->progressBar->getCurrentMessage();

        foreach ($models['Modelos'] as $model) {
            $this->progressBar->setCurrentMessage("{$currentMessage} -> {$model['Label']}");
            $this->requestParams['codigoModelo'] = $model['Value'];
            $this->updateYearModels();
        }
    }

    private function updateYearModels(): void
    {
        $yearModels = $this->responseProvider->run('ConsultarAnoModelo', $this->requestParams);
        $currentMessage = $this->progressBar->getCurrentMessage();

        foreach ($yearModels as $yearModel) {
            $yearModelLabel = substr($yearModel['Label'], 0, 4);
            $this->progressBar->setCurrentMessage("{$currentMessage} -> {$yearModel['Value']}/{$yearModelLabel}");
            $this->progressBar->show();
            $this->requestParams['ano'] = $yearModel['Value'];
            $this->requestParams['anoModelo'] = $yearModelLabel;
            //$this->updateModelThroughYears();
        }
    }

    private function updateModelThroughYears(): void
    {
        $models = $this->responseProvider->run('ConsultarModelosAtravesDoAno', $this->requestParams);
        
        foreach ($models as $model) {
            $this->requestParams['tipoConsulta'] = 'tradicional';
            //codigoTipoCombustivel
        }
    }

    private function updatePrice(): void
    {
        $this->responseProvider->run('ConsultarValorComTodosParametros', $this->requestParams);
    }
}
