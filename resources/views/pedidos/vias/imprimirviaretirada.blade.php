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
    width: 60%;
    margin-left: 0;
    margin-right: auto;
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



    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <h2 style="text-align: center;">Pedido #{{ $pedido->id }}</h2>

     <table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="padding: 5px; vertical-align: top;">
            <p><strong>Endereço:</strong> {{ $pedido->cliente->endereco }}</p>
            <p><strong>Nome:</strong> {{ $pedido->cliente->nome }}</p>
            
        </td>
        <td style="padding: 5px; vertical-align: top;">
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
            <p><strong>{{ $item->nomeItem }} </strong> 
            <hr>
        @endforeach
    </div>

            <p><strong>Observações: {{ $pedido->obs }} </strong> 

</div>
   @php
    $qtdImagens = count($pedido->imagens);
   $qtdItens = count($pedido->items);
   @endphp


   <div class="section">
           <h1 style="text-align: center;"><strong>Data:</strong> {{ $pedido->data_retirada }}</h1>
   </div>
</div>

</div>
</body>
</html>
