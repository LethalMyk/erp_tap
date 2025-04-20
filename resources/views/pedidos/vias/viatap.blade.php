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
<a href="{{ route('pedidos.imprimirviatap', $pedido->id) }}" target="_blank" class="btn btn-primary" style="margin-bottom: 20px;">
🖨️ Imprimir Via
</a>
</div>

