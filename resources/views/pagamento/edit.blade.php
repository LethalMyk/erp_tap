<x-app-layout>
<h2>Editar Pagamento</h2>

<form action="{{ route('pagamento.update', $pagamento->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Valor:</label>
    <input type="number" step="0.01" name="valor" value="{{ $pagamento->valor }}" required>

    <label>Forma de Pagamento:</label>
    <input type="text" name="forma" value="{{ $pagamento->forma }}" required>

    <label>Observações:</label>
    <textarea name="obs">{{ $pagamento->obs }}</textarea>

    <button type="submit">Salvar</button>
</form>

</x-app-layout>

