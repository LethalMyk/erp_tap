<!DOCTYPE html>
<html>
<head>
    <title>Impressão do Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #000;
            background: #fff;
        }
        .container {
            width: 90%;
            margin: 0 auto;
        }
        h2, h3 {
            margin-top: 10px;
        }
        .section {
            border-top: 1px solid #ccc;
            margin-top: 10px;
            padding-top: 10px;
        }
        table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    font-size: 13px;
}

td, th {
    padding: 3px 5px;
    vertical-align: top;
}

.imagens-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center; /* 👈 Centraliza as imagens */
    gap: 10px;
    margin-top: 15px;
}


/* 1-3 itens */
.itens-1 p,
.itens-2 p,
.itens-3 p {
    font-size: 16px;
}

/* 4-6 itens */
.itens-4 p,
.itens-5 p,
.itens-6 p
 {
    font-size: 14px;
}

/* 7-8 itens */
.itens-7 p,
.itens-8 p {
    font-size: 12px;
}

/* 9 ou mais */
.itens-9 p,
.itens-10 p,
.itens-11 p,
.itens-12 p,
.itens-13 p,
.itens-14 p,
.itens-15 p {
    font-size: 11px;
    margin-bottom: 2px;
}



/* 1-2 imagens */
.imagens-1 img,
.imagens-2 img {
    max-width: 50%;
}

/* 3-4 imagens */
.imagens-3 img,
.imagens-4 img {
    max-width: 30%;
}

/* 5 ou mais imagens */
.imagens-5 img,
.imagens-6 img,
.imagens-7 img,
.imagens-8 img,
.imagens-9 img,
.imagens-10 img {
    max-width: 20%;
}

/* Se quiser garantir um limite mínimo pra não ficarem muito pequenas */
.imagens-10plus img {
    max-width: 15%;
}

    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <h2 style="text-align: center;">Pedido #{{ $pedido->id }}</h2>

     <table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="padding: 5px; vertical-align: top;">
            <p><strong>Nome:</strong> {{ $pedido->cliente->nome }}</p>
            <p><strong>Endereço:</strong> {{ $pedido->cliente->endereco }}</p>
            <p><strong>Itens Retirados dia:</strong> {{ $pedido->dataRetirada }}</p>

        </td>
        <td style="padding: 5px; vertical-align: top;">
            <p><strong>Orçamento dia:</strong> {{ $pedido->data }}</p>
            <p><strong>Telefone:</strong> {{ $pedido->cliente->telefone }}</p>
        </td>
         </tr>
</table>


@php
    $qtdImagens = count($pedido->imagens);
    $qtdItens = count($pedido->items);
@endphp

<div class="container">
    <div class="section itens-{{ $qtdItens }}">

        @foreach($pedido->items as $item)
            <p><strong>{{ $item->nomeItem }} </strong> |
            <strong>Material:</strong> {{ $item->material }} |
            <strong>Metragem:</strong> {{ $item->metragem }} m</p>
            <p>{{ $item->especifi }}</p>
            <hr>
        @endforeach
    </div>



</div>
   @php
    $qtdImagens = count($pedido->imagens);
   $qtdItens = count($pedido->items);
   @endphp

<div class="imagens-container imagens-{{ $qtdImagens }}">

    @foreach($pedido->imagens as $imagem)
        <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do pedido">
    @endforeach
</div>

</div>

<div class="section">
        <h1 style="text-align: center;"><strong>Prazo de Entrega:</strong> {{ $pedido->prazo }}</h1>
</div>
</div>
</body>
</html>
