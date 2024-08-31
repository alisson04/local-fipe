# Local Fipe table

## Problemas que esse projeto visa resolver
* O site https://veiculos.fipe.org.br ocasionalmente fica fora do ar!
* Não existe uma API pública para acessar os dados da tabela Fipe!

## Solução proposta

<table>
    <tr>
        <td>Passo</td>
        <td>Descrição</td>
        <td>Status</td>
    </tr>
    <tr>
        <td>1</td>
        <td>Baixar todos os arquivos de resposta usados no site original da Fipe</td>
        <td>IN PROCESS</td>
    </tr>
    <tr>
        <td>2</td>
        <td>Disponibilizar os arquivos em um repositório publico para serem acessados via API</td>
        <td>WAITING</td>
    </tr>
    <tr>
        <td>2</td>
        <td>Disponibilizar uma biblioteca para ser usada como API no acesso aos arquivos</td>
        <td>WAITING</td>
    </tr>
</table>

## Tabela com rodas usadas para buscar os dados
* Rota principal: https://veiculos.fipe.org.br/api/veiculos;

<table>
    <tr>
        <td>Passo</td>
        <td>Rota</td>
        <td>Tipo</td>
        <td>Status dos responses</td>
    </tr>
    <tr>
        <td>1</td>
        <td>/ConsultarTabelaDeReferencia</td>
        <td>POST</td>
        <td>0/283</td>
    </tr>
    <tr>
        <td>2</td>
        <td>/ConsultarMarcas</td>
        <td>POST</td>
        <td>852/852</td>
    </tr>
    <tr>
        <td>3</td>
        <td>/ConsultarModelos</td>
        <td>POST</td>
        <td>142.374/142.374</td>
    </tr>
    </tr>
        <td>4</td>
        <td>/ConsultarAnoModelo</td>
        <td>POST</td>
        <td>1.775.114</td>
    </tr>
    </tr>
        <td>5</td>
        <td>/ConsultarModelosAtravesDoAno</td>
        <td>POST</td>
        <td></td>
    </tr>
    </tr>
        <td>6</td>
        <td>/ConsultarValorComTodosParametros</td>
        <td>POST</td>
        <td>4.000.519</td>
    </tr>
</table>

## Rotas e resultados
### Passo 1 - /ConsultarTabelaDeReferencia
```json
[
    {
        "Codigo": 311,
        "Mes": "julho/2024 "
    },
    {...}
]
```

### Passo 2 - /ConsultarMarcas
```json
[
    {
        "Label": "HONDA",
        "Value": "80"
    },
    {...}
]
```

### Passo 3 - /ConsultarModelos
```json
{
    "Modelos": [
        {
            "Label": "ADV 150",
            "Value": 9265
        },
        {...}
    ]
}
```

### Passo 4 - /ConsultarAnoModelo
```json
[
    {
        "Label": "2015",
        "Value": "2015-1"
    },
    {...}
]
```

### Passo 5 - /ConsultarModelosAtravesDoAno
```json
[
    {
        "Label": "BIZ 125 ES/ ES F.INJ./ES MIX F.INJECTION",
        "Value": "3841"
    },
    {...}
]
```

### Passo 6 - /ConsultarValorComTodosParametros
```json
{
    "Valor": "R$ 8.957,00",
    "Marca": "HONDA",
    "Modelo": "CG 150 CARGO ESD FLEX",
    "AnoModelo": 2014,
    "Combustivel": "Gasolina",
    "CodigoFipe": "811123-5",
    "MesReferencia": "julho de 2024 ",
    "Autenticacao": "cd6kfps5tj1q",
    "TipoVeiculo": 2,
    "SiglaCombustivel": "G",
    "DataConsulta": "sábado, 20 de julho de 2024 12:42"
}
```