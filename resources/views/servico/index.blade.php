<x-app-layout>

<h2>Lista de Serviços</h2>
<a href="{{ route('servico.create') }}" class="btn btn-primary">Novo Serviço</a>
<table class="table">
    <tr>
        <th>ID</th>
        <th>Profissional</th>
        <th>Pedido</th>
        <th>Data Início</th>
        <th>Data Previsão</th>
        <th>Ações</th>
    </tr>
    @foreach ($servicos as $servico)
    <tr>
        <td>{{ $servico->id }}</td>
        <td>{{ $servico->profissional->nome }}</td>
        <td>{{ $servico->pedido->id }}</td>
        <td>{{ $servico->data_inicio }}</td>
        <td>{{ $servico->data_previsao }}</td>
        <td>
            <a href="{{ route('servico.show', $servico->id) }}">Ver</a> |
            <a href="{{ route('servico.edit', $servico->id) }}">Editar</a> |
            <form action="{{ route('servico.destroy', $servico->id) }}" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit">Excluir</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>

</x-app-layout>
