    <x-app-layout>

<form action="{{ route('formulario.store') }}" method="POST" enctype="multipart/form-data">

    @csrf
    
<h3>Cliente</h3>
<input type="text" name="cliente[nome]" placeholder="Nome do Cliente" required>
<input type="text" name="cliente[telefone]" placeholder="Telefone" required>
<input type="text" name="cliente[endereco]" placeholder="Endereço" required>
<input type="text" name="cliente[cpf]" placeholder="CPF" required>
<input type="email" name="cliente[email]" placeholder="E-mail" required>

    <h3>Datas e Prazos</h3>

<div>
  <label for="data_pedido">Data do Pedido</label><br>
  <input type="date" id="data_pedido" name="pedido[data]" placeholder="Data do Pedido" required>
</div>

<div>
  <label for="data_retirada">Data de Retirada</label><br>
  <input type="date" id="data_retirada" name="pedido[data_retirada]" placeholder="Data de Retirada">
</div>

<div>
  <label for="prazo">Prazo</label><br>
  <input type="date" id="prazo" name="pedido[prazo]" placeholder="Prazo">
</div>

<br><br><br>


<h3>Itens</h3>
<div id="itens">
    
    <div class="item">
        <input type="text" name="items[0][nomeItem]" placeholder="Nome do Item" required>
        <input type="text" name="items[0][material]" placeholder="Material" required>
        <input type="number" name="items[0][metragem]" placeholder="Metragem" step="0.01" required>
        <select name="items[0][material_disponib]" required>
    <option value="Pedir" selected>Pedir</option>
    <option value="Complementar">Complementar</option>
    <option value="TM">TM</option>
</select>
        <input type="text" name="items[0][especifi]" placeholder="Especificação">
        <button type="button" onclick="removerItem(this)">Remover Item</button>
        
        <h4>Serviços Terceirizados</h4>
        <div class="terceirizadas-container" id="terceirizadas-0">
            <!-- Serviços terceirizados serão adicionados aqui -->
            <button type="button" onclick="addTerceirizada(0)">+ Adicionar Terceirizadas</button>
        </div>
    </div>
</div>
<button type="button" onclick="addItem()">+ Adicionar Item</button>


<br><br>
    
    <h3>Imagens do Pedido</h3>
<input type="file" name="imagens[]" multiple accept="image/*">
    
<br><br>

<label for="tapeceiro">Tapeceiro:</label>
<select name="pedido[tapeceiro]" id="tapeceiro" class="form-control">
<option value="">Distribuir</option>
    @foreach($profissionais as $prof)
        <option value="{{ $prof->id }}">{{ $prof->nome }}</option>
    @endforeach
</select>


<h3>Resumo Final</h3>
    <input type="number" step="0.01" name="pedido[valor]" placeholder="Valor Total" required>


<h3>Pagamentos</h3>
<div id="pagamentos">
    <div class="pagamento">
    <input type="number" step="0.01" name="pagamentos[0][valor]" placeholder="Valor" required>
    <select name="pagamentos[0][forma]" required onchange="toggleDataPagamento(this)">
        <option value="">Selecione</option>
        <option value="PIX">PIX</option>
        <option value="DEBITO">DEBITO</option>
        <option value="DINHEIRO">DINHEIRO</option>
        <option value="CREDITO À VISTA">CREDITO À VISTA</option>
        <option value="CREDITO PARCELADO">CREDITO PARCELADO</option>
        <option value="BOLETO">BOLETO</option>
        <option value="CHEQUE">CHEQUE</option>
        <option value="OUTROS">OUTROS</option>
    </select>
    <input type="text" name="pagamentos[0][obs]" placeholder="Observação">
    
    <!-- Campo data, inicialmente escondido -->
    <input type="date" name="pagamentos[0][data]" placeholder="Data do Pagamento" style="display:none; margin-top:10px;">
    
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
        <select name="items[${itemIndex}][material_disponib]" required>
    <option value="Pedir" selected>Pedir</option>
    <option value="Complementar">Complementar</option>
    <option value="TM">TM</option>
</select>

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
        <select name="pagamentos[${pagamentoIndex}][forma]" required onchange="toggleDataPagamento(this)">
            <option value="">Selecione</option>
            <option value="PIX">PIX</option>
            <option value="DEBITO">DEBITO</option>
            <option value="DINHEIRO">DINHEIRO</option>
            <option value="CREDITO À VISTA">CREDITO À VISTA</option>
            <option value="CREDITO PARCELADO">CREDITO PARCELADO</option>
            <option value="BOLETO">BOLETO</option>
            <option value="CHEQUE">CHEQUE</option>
            <option value="OUTROS">OUTROS</option>
        </select>
        <input type="text" name="pagamentos[${pagamentoIndex}][obs]" placeholder="Observação">
        <input type="date" name="pagamentos[${pagamentoIndex}][data]" placeholder="Data do Pagamento" style="display:none; margin-top:10px;">
        <button type="button" onclick="removerPagamento(this)">Remover</button>
    `;
    wrapper.appendChild(newPagamento);
    pagamentoIndex++;
}

function toggleDataPagamento(select) {
    const pagamentoDiv = select.closest('.pagamento');
    const inputData = pagamentoDiv.querySelector('input[type="date"]');
    if (select.value === 'OUTROS') {
        inputData.style.display = 'block';
        inputData.required = true;
    } else {
        inputData.style.display = 'none';
        inputData.required = false;
        inputData.value = ''; // limpa o valor quando esconde
    }
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
<form action="{{ route('formulario.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        form {
            width: 60%;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h3 {
            color: #333;
            font-size: 1.4em;
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="file"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .item, .pagamento, .terceirizada {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }

        .item:hover,
        .pagamento:hover,
        .terceirizada:hover {
            border-color: #4CAF50;
        }

        .item input,
        .pagamento input {
            width: calc(100% - 22px); /* Ajuste para o padding */
            margin-bottom: 10px;
        }

        .terceirizada input {
            width: calc(100% - 32px); /* Ajuste para o padding do botão */
        }

        .terceirizadas-container {
            margin-top: 10px;
        }

        .terceirizada button,
        .item button,
        .pagamento button {
            background-color: #f44336;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-size: 0.9em;
            cursor: pointer;
            border: none;
            transition: background-color 0.3s;
        }

        .terceirizada button:hover,
        .item button:hover,
        .pagamento button:hover {
            background-color: #e53935;
        }

        .terceirizadas-container {
            margin-top: 15px;
        }

        button[type="submit"] {
            width: 100%;
            background-color: #2196F3;
            padding: 12px;
            border-radius: 5px;
            color: white;
            font-size: 1.2}
    </x-app-layout>
