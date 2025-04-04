
<form action="{{ route('formulario.store') }}" method="POST" enctype="multipart/form-data">

    @csrf
    <style>
    .item {
        margin-bottom: 20px; /* Ajuste o valor conforme necessário */
        padding: 10px; /* Adiciona padding dentro de cada item */
        border: 1px solid #ccc; /* Opcional: Adiciona uma borda para destacar o item */
        border-radius: 5px; /* Opcional: Deixa as bordas arredondadas */
        background-color: #f9f9f9; /* Opcional: Muda o fundo para um cinza claro */
    }

    .terceirizada {
        margin-top: 10px; /* Adiciona espaçamento entre os serviços terceirizados */
        margin-bottom: 10px; /* Espaçamento após o serviço */
        padding: 10px;
        border: 1px solid #ddd;
        background-color: #f1f1f1;
        border-radius: 5px;
    }

    .pagamento {
        margin-bottom: 15px; /* Espaçamento entre os pagamentos */
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f1f1f1;
    }

    /* Aumentando o espaçamento do botão "Adicionar" */
    button {
        margin-top: 10px; /* Adiciona um pequeno espaço entre o botão e o conteúdo anterior */
    }

    .terceirizadas-container {
        margin-top: 10px;
    }
</style>

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
<br><br><br>
    <button type="button" onclick="addItem()">+ Adicionar Item</button>

    <h3>Itens</h3>
    <div id="itens">
        
        <div class="item">
            <input type="text" name="items[0][nomeItem]" placeholder="Nome do Item" required>
            <input type="text" name="items[0][material]" placeholder="Material" required>
            <input type="number" name="items[0][metragem]" placeholder="Metragem" step="0.01" required>
            <input type="text" name="items[0][especifi]" placeholder="Especificação">
            <button type="button" onclick="removerItem(this)">Remover Item</button>

            <h4>Serviços Terceirizados</h4>
            <div class="terceirizadas-container" id="terceirizadas-0">
                <!-- Serviços terceirizados serão adicionados aqui -->
                <button type="button" onclick="addTerceirizada(0)">+ Adicionar Terceirizadas</button>
            </div>
        </div>
    </div>


<br><br>
    
    <h3>Imagens do Pedido</h3>
<input type="file" name="imagens[]" multiple accept="image/*">
    
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
let terceirizadaIndex = {0: 0}; // Índice para cada item

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

        <h4>Serviços Terceirizados</h4>
        <div class="terceirizadas-container" id="terceirizadas-${itemIndex}">
            <!-- Serviços terceirizados serão adicionados aqui -->
        </div>
        <button type="button" onclick="addTerceirizada(${itemIndex})">+ Adicionar Terceirizadas</button>
    `;

    wrapper.appendChild(newItem);
    terceirizadaIndex[itemIndex] = 0; // Inicializa contador para terceirizadas
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

function addTerceirizada(itemIdx) {
    const container = document.getElementById(`terceirizadas-${itemIdx}`);
    const newTerceirizada = document.createElement('div');
    newTerceirizada.classList.add('terceirizada');
    newTerceirizada.innerHTML = `
        <input type="text" name="items[${itemIdx}][terceirizadas][${terceirizadaIndex[itemIdx]}][tipo]" placeholder="Tipo de Serviço" required>
        <input type="text" name="items[${itemIdx}][terceirizadas][${terceirizadaIndex[itemIdx]}][obs]" placeholder="Observação">
        <button type="button" onclick="removerTerceirizada(this)">Remover</button>
    `;
    container.appendChild(newTerceirizada);
    terceirizadaIndex[itemIdx]++;
}

function removerItem(button) {
    const wrapper = document.getElementById('itens');
    if (wrapper.children.length > 1) {
        button.closest('.item').remove();
    } else {
        alert("Você não pode remover o último item.");
    }
}

function removerPagamento(button) {
    const wrapper = document.getElementById('pagamentos');
    if (wrapper.children.length > 1) {
        button.closest('.pagamento').remove();
    } else {
        alert("Você não pode remover o último pagamento.");
    }
}

function removerTerceirizada(button) {
    button.closest('.terceirizada').remove();
}
</script>