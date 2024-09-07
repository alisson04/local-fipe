<?php

use Src\GenerateLocalReponsePath;

test('it should generate path to references', function () {
    $generateLocalReponsePath = new GenerateLocalReponsePath();
    $path = $generateLocalReponsePath->run('ConsultarTabelaDeReferencia', []);
    expect($path)->toEqual('references');
});
