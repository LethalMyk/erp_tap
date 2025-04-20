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

<a href="{{ route('pedidos.imprimirviaretirada', $pedido->id) }}" target="_blank" class="btn btn-primary" style="margin-bottom: 20px;">
🖨️ Imprimir Via
</a>
</div>

