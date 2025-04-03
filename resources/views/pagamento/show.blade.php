<x-app-layout>
<h2>Detalhes do Pagamento</h2>

<p><strong>ID:</strong> {{ $pagamento->id }}</p>
<p><strong>Pedido:</strong> {{ $pagamento->pedido->id }}</p>
<p><strong>Valor:</strong> R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</p>
<p><strong>Forma:</strong> {{ $pagamento->forma }}</p>
<p><strong>Observações:</strong> {{ $pagamento->obs ?? 'Nenhuma' }}</p>

<a href="{{ route('pagamento.edit', $pagamento->id) }}" class="btn btn-primary">Editar</a>

<form action="{{ route('pagamento.destroy', $pagamento->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">Excluir</button>
</form>

<a href="{{ route('pagamento.index') }}" class="btn btn-secondary">Voltar</a>
</x-app-layout>
