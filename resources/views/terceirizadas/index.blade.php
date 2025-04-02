<x-app-layout>
    <h1>Lista de Serviços Terceirizados</h1>
    <a href="{{ route('terceirizadas.create') }}">Novo Serviço</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tipo de Serviço</th>
                <th>Item Relacionado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($terceirizadas as $terceirizada)
                <tr>
                    <td>{{ $terceirizada->id }}</td>
                    <td>{{ $terceirizada->tipoServico }}</td>
<td>
    {{ $terceirizada->item ? $terceirizada->item->nomeItem : 'Sem item associado' }}
</td>
                    <td>
                        <a href="{{ route('terceirizadas.show', $terceirizada->id) }}"> 👁️ Ver</a>
                        <a href="{{ route('terceirizadas.edit', $terceirizada->id) }}">✏️ Editar</a>
                        <form action="{{ route('terceirizadas.destroy', $terceirizada->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Deseja excluir este serviço terceirizado?')">🗑️ Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-app-layout>
