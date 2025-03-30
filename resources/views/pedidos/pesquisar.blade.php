<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pesquisar Pedidos
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
<form method="GET" action="{{ route('pedidos.pesquisar') }}">
    <input type="text" name="termo" placeholder="Buscar por ID, CPF, Nome ou Endereço..." value="{{ request('termo') }}" class="border rounded p-2">
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Pesquisar</button>
</form>

        @if($pedidos->count())
            <table class="mt-4 border w-full">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>CPF</th>
                        <th>Endereço</th>
                        <th>Status</th>
                        <th>Data</th>
                        <th>Ações</th> <!-- Nova coluna de ações -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos as $pedido)
                        <tr>
                            <td>{{ $pedido->pedido_id }}</td>
                            <td>{{ $pedido->client->nome ?? 'Cliente não encontrado' }}</td>
                            <td>{{ $pedido->client->cpf ?? '-' }}</td>
                            <td>{{ $pedido->client->endereco ?? '-' }}</td>
                            <td>{{ $pedido->status }}</td>
                            <td>{{ $pedido->created_at }}</td>
                            <td>
                                <!-- Botão para redirecionar ao formulário de pagamento -->
                                <a href="{{ route('pagamento.form', $pedido->pedido_id) }}" class="bg-green-500 text-white px-4 py-2 rounded">Registrar Pagamento</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $pedidos->links() }}
        @else
            <p class="text-red-500">Nenhum pedido encontrado.</p>
        @endif
    </div>
</x-app-layout>
