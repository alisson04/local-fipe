# Local Fipe table

## Problemas que esse projeto visa resolver
* O site https://veiculos.fipe.org.br ocasionalmente fica fora do ar!
* Não existe uma API pública!
* Não há possibilidade de realizar consultas com filtros específicos no site!

## Solução proposta
1. Analisar as entidades e propriedades usadas no site para estruturação da base de dados.
2. Montar um ambiente Docker para acesso aos dados.
3. Montar uma base de dados com as mesmas informações do site.
4. Disponibilizar esses dados como backup dentro do repositório.

## Problemas encontrados
1. A quantidade excessiva de arquivos pesa a IDE e o sistema do git

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
        <td>47.458/47.458</td>
    </tr>
    </tr>
        <td>4</td>
        <td>/ConsultarAnoModelo</td>
        <td>POST</td>
        <td></td>
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
        <td></td>
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