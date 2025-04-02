<x-app-layout>
    <h1>Detalhes do Pedido #{{ $pedido->id }}</h1>

    <p><strong>Cliente:</strong> {{ $pedido->cliente->nome }}</p>
    <p><strong>Quantidade de Itens:</strong> {{ $pedido->qntItens }}</p>
    <p><strong>Data do Pedido:</strong> {{ $pedido->data }}</p>
    <p><strong>Valor:</strong> {{ $pedido->valor }}</p>
    <p><strong>Status:</strong> {{ $pedido->status }}</p>
    <p><strong>Observações:</strong> {{ $pedido->obs }}</p>
    <p><strong>Prazo:</strong> {{ $pedido->prazo }}</p>

    <h3>Imagens do Pedido:</h3>
    @if ($pedido->imagens->count())
        @foreach ($pedido->imagens as $imagem)
            <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do Pedido" width="200">
        @endforeach
    @else
        <p>Sem imagens anexadas.</p>
    @endif

    <a href="{{ route('pedidos.index') }}">Voltar</a>
</x-app-layout>
