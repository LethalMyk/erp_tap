 <x-app-layout>
    <h2>Novo Pagamento</h2>
<form action="{{ route('pagamento.store') }}" method="POST">
    @csrf
    <label>Pedido:</label>
<select name="pedido_id" id="pedido_id" onchange="mostrarDadosPedido()" required>
    <option value="">Selecione um pedido</option>
    @foreach($pedidos as $pedido)
        <option 
            value="{{ $pedido->id }}"
            data-nome="{{ $pedido->cliente->nome }}"
            data-endereco="{{ $pedido->cliente->endereco }}"
            data-telefone="{{ $pedido->cliente->telefone }}"
        >
            Pedido #{{ $pedido->id }} - {{ $pedido->cliente->nome }}
        </option>
    @endforeach
</select>
<div id="dados-cliente" style="margin-top: 15px; display: none; border: 1px solid #ccc; padding: 10px; border-radius: 5px;">
    <p><strong>Cliente:</strong> <span id="nome-cliente"></span></p>
    <p><strong>Endereço:</strong> <span id="endereco-cliente"></span></p>
    <p><strong>Telefone:</strong> <span id="telefone-cliente"></span></p>
</div>


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
<div id="data-pagamento-esperada-group" style="display: none; margin-top: 10px;">
    <label for="data">Data Esperada do Pagamento:</label>
    <input type="date" name="data" id="data">
</div>



    <label>Observações:</label>
    <textarea name="obs"></textarea>

    <button type="submit">Salvar</button>
</form>
<script>
    function mostrarDadosPedido() {
        const select = document.getElementById('pedido_id');
        const option = select.options[select.selectedIndex];

        const nome = option.getAttribute('data-nome');
        const endereco = option.getAttribute('data-endereco');
        const telefone = option.getAttribute('data-telefone');

        if (nome && endereco && telefone) {
            document.getElementById('nome-cliente').textContent = nome;
            document.getElementById('endereco-cliente').textContent = endereco;
            document.getElementById('telefone-cliente').textContent = telefone;
            document.getElementById('dados-cliente').style.display = 'block';
        } else {
            document.getElementById('dados-cliente').style.display = 'none';
        }
    }

    
</script>
<script>
    document.getElementById('forma').addEventListener('change', function () {
        const valor = this.value;
        const grupoData = document.getElementById('data-pagamento-esperada-group');

        if (['BOLETO', 'CHEQUE', 'OUTROS'].includes(valor)) {
            grupoData.style.display = 'block';
        } else {
            grupoData.style.display = 'none';
            document.getElementById('data').value = ''; // limpa campo quando não precisa
        }
    });
</script>


</x-app-layout>
