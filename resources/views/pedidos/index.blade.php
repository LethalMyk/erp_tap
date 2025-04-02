<x-app-layout>
    <h1>Lista de Pedidos</h1>
    <a href="{{ route('pedidos.create') }}">Criar Pedido</a>


    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Quantidade de Itens</th>
                <th>Data</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $pedido)
                <tr>
                    <td>{{ $pedido->id }}</td>
                    <td>{{ $pedido->cliente ? $pedido->cliente->nome : 'Cliente não encontrado' }}</td>
                    <td>{{ $pedido->qntItens }}</td>
                    <td>{{ $pedido->data }}</td>
                    <td>{{ $pedido->valor }}</td>
                    <td>{{ $pedido->status }}</td>
                    <td>
                        <a href="{{ route('pedidos.show', $pedido->id) }}">Ver</a>
                        <a href="{{ route('pedidos.edit', $pedido->id) }}">Editar</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-app-layout>
