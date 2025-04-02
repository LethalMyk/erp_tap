
 <x-app-layout>
 // Views - resources/views/items/index.blade.php
    <h1>Lista de Itens</h1>
    <a href="{{ route('items.create') }}">Criar Novo Item</a>
    <table>
        <tr><th>ID</th><th>Nome</th><th>Material</th><th>Metragem</th><th>Ações</th></tr>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->nomeItem }}</td>
                <td>{{ $item->material }}</td>
                <td>{{ $item->metragem }}</td>
                <td>
                    <a href="{{ route('items.show', $item->id) }}">Ver</a>
                    <a href="{{ route('items.edit', $item->id) }}">Editar</a>
                    <form action="{{ route('items.destroy', $item->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit">Excluir</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</x-app-layout>
