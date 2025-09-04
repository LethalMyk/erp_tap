<!DOCTYPE html>
<html>
<head>
    <title>Impressão do Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px; /* fonte geral */
            color: #000;
            background: #fff;
            margin: 6mm; /* margem da página */
        }

        @page {
            size: A4;
            margin: 6mm;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* Alinhar conteúdo da célula direita */
        td.right {
            text-align: right;
        }

        /* Pedido # maior */
        h2 {
            font-size: 18px;
            margin: 5px 0;
            text-align: center;
        }

        h1, h3 {
            font-size: 12px;
            margin: 3px 0;
            text-align: center;
        }

        .section {
            border-top: 1px solid #ccc;
            margin-top: 5px;
            padding-top: 5px;
            page-break-inside: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        td, th {
            padding: 2px 4px;
            vertical-align: top;
        }

        /* Campos principais maiores */
        .destaque {
            font-size: 13px;
            font-weight: bold;
        }

        .imagens-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            margin-top: 5px;
            page-break-inside: avoid;
        }

        /* Tamanho padrão até 3 imagens */
        .imagens-container img {
            display: block;
            height: auto;
            page-break-inside: avoid;
            max-width: 25%;
        }

        /* A partir da 4ª imagem, diminui */
        .imagens-container img:nth-child(n+4) {
            max-width: 12%;
        }

        /* Fonte adaptável por quantidade de itens */
        .itens-1 p,
        .itens-2 p,
        .itens-3 p,
        .itens-4 p,
        .itens-5 p {
            font-size: 10px;
        }

        .itens-6 p,
        .itens-7 p,
        .itens-8 p {
            font-size: 9.5px;
        }

        .itens-9 p,
        .itens-10 p,
        .itens-11 p,
        .itens-12 p,
        .itens-13 p {
            font-size: 9px;
            margin-bottom: 1px;
        }

        hr {
            margin: 3px 0;
        }

        /* Estilo especial para o prazo de entrega */
        .prazo-entrega {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin: 10px 0;
            color: #000;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">

        <h2>Pedido #{{ $pedido->id }}</h2>

        <table>
            <tr>
                <td>
                    <p class="destaque"><strong>Nome:</strong> {{ $pedido->cliente->nome }}</p>
                    <p class="destaque"><strong>Endereço:</strong> {{ $pedido->cliente->endereco }}</p>
                    <p class="destaque"><strong>Telefone:</strong> {{ $pedido->cliente->telefone }}</p>
                </td>
                <td class="right">
                    <p class="destaque"><strong>Orçamento dia:</strong> {{ $pedido->data }}</p>
                    <p class="destaque"><strong>Itens Retirados dia:</strong> {{ $pedido->dataRetirada }}</p>
                </td>
            </tr>
        </table>

        @php
            $qtdImagens = count($pedido->imagens);
            $qtdItens = count($pedido->items);
        @endphp

        <div class="section itens-{{ $qtdItens }}">
            @foreach($pedido->items as $item)
                <p><strong>{{ $item->nomeItem }}</strong> |
                <strong>Material:</strong> {{ $item->material }} |
                <strong>Metragem:</strong> {{ $item->metragem }} m</p>
                <p>{{ $item->especifi }}</p>
                <hr>
            @endforeach
        </div>

        @if($qtdImagens > 0)
        <div class="imagens-container imagens-{{ $qtdImagens }}">
            @foreach($pedido->imagens as $imagem)
                <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do pedido">
            @endforeach
        </div>
        @endif

        <div class="section">
            <p class="prazo-entrega"><strong>Prazo de Entrega:</strong> {{ $pedido->prazo }}</p>
        </div>
    </div>
</body>
</html>
