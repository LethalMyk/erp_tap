 <x-app-layout>
    <h2>Novo Pagamento</h2>
<form action="{{ route('pagamento.store') }}" method="POST">
    @csrf
    <label>Pedido:</label>
    <select name="pedido_id">
        @foreach($pedidos as $pedido)
            <option value="{{ $pedido->id }}">{{ $pedido->id }}</option>
        @endforeach
    </select>

    <label>Valor:</label>
    <input type="number" step="0.01" name="valor" required>
<div>
    <label for="forma">Forma de Pagamento</label>
    <select name="forma" id="forma" required>
        <option value="PIX">PIX</option>
        <option value="DEBITO">Débito</option>
        <option value="DINHEIRO">Dinheiro</option>
        <option value="CREDITO À VISTA">Crédito à Vista</option>
        <option value="CREDITO PARCELADO">Crédito Parcelado</option>
        <option value="BOLETO">Boleto</option>
        <option value="CHEQUE">Cheque</option>
        <option value="OUTROS">Outros</option>
    </select>
</div>


    <label>Observações:</label>
    <textarea name="obs"></textarea>

    <button type="submit">Salvar</button>
</form>
</x-app-layout>
