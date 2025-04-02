<x-app-layout>

    <h2>Lista de Profissionais</h2>
    <a href="{{ route('profissional.create') }}">Adicionar Profissional</a>
    <ul>
        @foreach($profissional as $profissional)
            <li>{{ $profissional->nome }} - {{ $profissional->cargo }}
                <a href="{{ route('profissional.show', $profissional) }}">Ver</a> |
                <a href="{{ route('profissional.edit', $profissional) }}">Editar</a> |
                <form action="{{ route('profissional.destroy', $profissional) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Excluir</button>
                </form>
            </li>
        @endforeach
    </ul>

</x-app-layout>
