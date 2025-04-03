<x-app-layout>

<h2>Lista de Pagamentos</h2>
<a href="{{ route('pagamento.create') }}" class="btn btn-primary">Novo Pagamento</a>
<table class="table">
    <tr>
        <th>ID</th>
        <th>Pedido</th>
        <th>Valor</th>
        <th>Forma</th>
        <th>Ações</th>
    </tr>
    @foreach ($pagamentos as $pagamento)
    <tr>
        <td>{{ $pagamento->id }}</td>
        <td>{{ $pagamento->pedido->id }}</td>
        <td>R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</td>
        <td>{{ $pagamento->forma }}</td>
        <td>
            <a href="{{ route('pagamento.show', $pagamento->id) }}">Ver</a> |
            <a href="{{ route('pagamento.edit', $pagamento->id) }}">Editar</a> |
            <form action="{{ route('pagamento.destroy', $pagamento->id) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit">Excluir</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
</table>

</x-app-layout>
