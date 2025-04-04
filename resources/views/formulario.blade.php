
<form action="{{ route('formulario.store') }}" method="POST">
    @csrf
    
<h3>Cliente</h3>
<input type="text" name="cliente[nome]" placeholder="Nome do Cliente" required>
<input type="text" name="cliente[telefone]" placeholder="Telefone" required>
<input type="text" name="cliente[endereco]" placeholder="Endereço" required>
<input type="text" name="cliente[cpf]" placeholder="CPF" required>
<input type="email" name="cliente[email]" placeholder="E-mail" required>

    <h3>Pedido</h3>
    <input type="date" name="pedido[data]" placeholder="Data do Pedido" required>
    <input type="date" name="pedido[data_retirada]" placeholder="Data de Retirada">
    <input type="date" name="pedido[prazo]" placeholder="Prazo">
    <input type="number" step="0.01" name="pedido[valor]" placeholder="Valor Total" required>
    <input type="number" step="0.01" name="pedido[valor_resta]" placeholder="Valor Restante">

    <h3>Itens</h3>
    <div id="itens">
        <div class="item">
<input type="text" name="items[0][nomeItem]" />
<input type="text" name="items[0][material]" placeholder="Material">
<input type="text" name="items[0][metragem]" placeholder="Metragem">
<input type="text" name="items[0][especifi]" placeholder="Especificação">
    <button type="button" onclick="removerItem(this)">Remover</button>

</div>
    </div>
    <button type="button" onclick="addItem()">+ Adicionar Item</button>
<br><br>


<h3>Pagamentos</h3>
<div id="pagamentos">
    <div class="pagamento">
        <input type="number" step="0.01" name="pagamentos[0][valor]" placeholder="Valor">
        <input type="text" name="pagamentos[0][forma]" placeholder="Forma de Pagamento">
        <input type="text" name="pagamentos[0][obs]" placeholder="Observação">
            <button type="button" onclick="removerPagamento(this)">Remover</button>

    </div>
</div>

<button type="button" onclick="addPagamento()">+ Adicionar Pagamento</button>

    <button type="submit">Salvar</button>
</form>

<script>
let itemIndex = 1;
let pagamentoIndex = 1;

function addItem() {
    const wrapper = document.getElementById('itens');
    const newItem = document.createElement('div');
    newItem.classList.add('item');
    newItem.innerHTML = `
        <input type="text" name="items[${itemIndex}][nomeItem]" placeholder="Nome do Item" required>
        <input type="text" name="items[${itemIndex}][material]" placeholder="Material" required>
        <input type="number" name="items[${itemIndex}][metragem]" placeholder="Metragem" step="0.01" required>
        <input type="text" name="items[${itemIndex}][especifi]" placeholder="Especificações">
        <button type="button" onclick="removerItem(this)">Remover</button>
    `;
    wrapper.appendChild(newItem);
    itemIndex++;
}

function addPagamento() {
    const wrapper = document.getElementById('pagamentos');
    const newPagamento = document.createElement('div');
    newPagamento.classList.add('pagamento');
    newPagamento.innerHTML = `
        <input type="number" step="0.01" name="pagamentos[${pagamentoIndex}][valor]" placeholder="Valor" required>
        <input type="text" name="pagamentos[${pagamentoIndex}][forma]" placeholder="Forma de Pagamento" required>
        <input type="text" name="pagamentos[${pagamentoIndex}][obs]" placeholder="Observação">
        <button type="button" onclick="removerPagamento(this)">Remover</button>
    `;
    wrapper.appendChild(newPagamento);
    pagamentoIndex++;
}

function removerItem(button) {
    const wrapper = document.getElementById('itens');
    if (wrapper.children.length > 1) {
        const item = button.closest('.item');
        item.remove();
    } else {
        alert("Você não pode remover o último item.");
    }
}

function removerPagamento(button) {
    const wrapper = document.getElementById('pagamentos');
    if (wrapper.children.length > 1) {
        const pagamento = button.closest('.pagamento');
        pagamento.remove();
    } else {
        alert("Você não pode remover o último pagamento.");
    }
}
</script>
