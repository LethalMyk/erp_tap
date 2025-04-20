 
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
    font-size: 12px;
}

/* 4-6 itens */
.itens-4 p,
.itens-5 p,
.itens-6 p
 {
    font-size: 9px;
}

/* 7-8 itens */
.itens-7 p,
.itens-8 p {
    font-size: 9px;
}

/* 9 ou mais */
.itens-9 p,
.itens-10 p,
.itens-11 p,
.itens-12 p,
.itens-13 p,
.itens-14 p,
.itens-15 p {
    font-size: 7px;
    margin-bottom: 2px;
}



/* 1-2 imagens */
.imagens-1 img,
.imagens-2 img {
    max-width: 150%;
}

/* 3-4 imagens */
.imagens-3 img,
.imagens-4 img {
    max-width: 60%;
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
       


    <div class="container">

        <h2>Visualização do Pedido</h2>

        <h3>Cliente</h3>
        <ul>
            <li><strong>Nome:</strong> {{ $pedido->cliente->nome }}</li>
            <li><strong>Telefone:</strong> {{ $pedido->cliente->telefone }}</li>
            <li><strong>Endereço:</strong> {{ $pedido->cliente->endereco }}</li>
            <li><strong>CPF:</strong> {{ $pedido->cliente->cpf }}</li>
            <li><strong>Email:</strong> {{ $pedido->cliente->email }}</li>
        </ul>

        <h3>Pedido</h3>
        <ul>
            <li><strong>Data:</strong> {{ $pedido->data }}</li>
            <li><strong>Data de Retirada:</strong> {{ $pedido->data_retirada }}</li>
            <li><strong>Prazo:</strong> {{ $pedido->prazo }}</li>
            <li><strong>Valor Total:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</li>
            <li><strong>Valor Restante:</strong> R$ {{ number_format($pedido->valor_resta, 2, ',', '.') }}</li>
        </ul>

        <h3>Itens</h3>
        @foreach($pedido->items as $item)
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                <p><strong>Nome do Item:</strong> {{ $item->nomeItem }}</p>
                <p><strong>Material:</strong> {{ $item->material }}</p>
                <p><strong>Metragem:</strong> {{ $item->metragem }} m</p>
                <p><strong>Especificações:</strong> {{ $item->especifi }}</p>

                <h4>Serviços Terceirizados</h4>
                @forelse($item->terceirizadas as $terc)
                    <p>- {{ $terc->tipoServico }} (Obs: {{ $terc->obs }})</p>
                @empty
                    <p>Sem serviços terceirizados</p>
                @endforelse
            </div>
        @endforeach

        <div style="border: 2px solid #000; padding: 15px; margin: 20px 0; border-radius: 8px;">
            <h3 style="color:rgb(22, 22, 22);">Valor do Pedido</h3>
            <p><strong>Total:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</p>
            <p><strong>Valor Restante:</strong> R$ {{ number_format($pedido->valor_resta, 2, ',', '.') }}</p>
        </div>

        <h3>Pagamentos Realizados</h3>
        @foreach($pedido->pagamentos as $pagamento)
            <div>
                <p><strong>Valor:</strong> R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</p>
                <p><strong>Forma:</strong> {{ $pagamento->forma }}</p>
                <p><strong>Obs:</strong> {{ $pagamento->obs }}</p>
            </div>
        @endforeach

        <h3>Imagens</h3>
        <div style="display: flex; flex-wrap: wrap;">
            @foreach($pedido->imagens as $imagem)
                <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do pedido" style="max-width: 200px; margin: 10px;">
            @endforeach
        </div>

        </div>

</body>
</html>
