 <x-app-layout>
    <h2>Adicionar Profissional</h2>
    <form action="{{ route('profissional.store') }}" method="POST">
        @csrf
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <label>Cargo:</label>
        <input type="text" name="cargo" required>
        <button type="submit">Salvar</button>
    </form>
</x-app-layout>
