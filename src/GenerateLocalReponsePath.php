<?php

namespace Src;

class GenerateLocalReponsePath
{
    private CONST MAP_URIS = [
        'ConsultarTabelaDeReferencia' => 'references',
        'ConsultarMarcas' => 'brands',
        'ConsultarModelos' => 'models',
        'ConsultarAnoModelo' => 'year-model',
        'ConsultarValorComTodosParametros' => 'prices',
    ];

    private CONST MAP_PARAMS = [
        'codigoTabelaReferencia' => 'reference',
        'codigoTipoVeiculo' => 'vehicle-type',
    ];

    private CONST MAP_URI_NECESSARY_PARAMS = [
        'ConsultarTabelaDeReferencia' => [],
        'ConsultarMarcas' => ['codigoTabelaReferencia', 'codigoTipoVeiculo'],
    ];

    public function run(string $uri, array $formParams = []): string
    {
        $this->validateUriAndParams($uri, $formParams);

        $filePath = self::MAP_URIS[$uri];

        foreach ($formParams as $key => $value) {
            $filePath .= self::MAP_PARAMS[$key] . "/{$value}/";
        }

        return $filePath;
    }

    private function validateUriAndParams(string $uri, array $formParams): void
    {
        $formParamsKeys = array_keys($formParams);
        $invalidUri = ! isset(self::MAP_URIS[$uri]);
        if ($invalidUri) {
            throw new \Exception("Uri not found: {$uri}");
        }

        $invalidParams = array_diff($formParamsKeys, self::MAP_PARAMS);
        if (! empty($invalidParams)) {
            throw new \Exception("Invalid params found: " . implode(', ', $invalidParams));
        }

        $uriNecessaryParams = self::MAP_URI_NECESSARY_PARAMS[$uri];
        $invalidParams = array_merge(
            array_diff($formParamsKeys, $uriNecessaryParams),
            array_diff($uriNecessaryParams, $formParamsKeys)
        );
        if (! empty($invalidParams)) {
            throw new \Exception("Invalid params for uri found: " . implode(', ', $invalidParams));
        }
    }
}