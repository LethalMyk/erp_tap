<x-app-layout>

    <h2>Editar Profissional</h2>
<form action="{{ route('profissional.update', $profissional->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label>Nome:</label>
        <input type="text" name="nome" value="{{ $profissional->nome }}" required>
        <label>Cargo:</label>
        <input type="text" name="cargo" value="{{ $profissional->cargo }}" required>
        <button type="submit">Atualizar</button>
    </form>
</x-app-layout>

