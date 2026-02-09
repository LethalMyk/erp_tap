<x-app-layout>
    <x-slot name="header">
        <datalist id="servicos-terceirizados">
            <option value="Impermeabilização">
            <option value="Higienização">
            <option value="Invernização">
            <option value="Pintura">
            <option value="Outros">
        </datalist>

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Formulário de Pedido</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <form action="{{ route('pedidos.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario()">
            @csrf

            <!-- Data do Pedido -->
            <div class="flex-1 mb-4">
                <label>Data do Pedido</label>
                <input type="date" name="data" required class="w-min border border-gray-300 rounded px-2 py-1">
            </div>

            <!-- Cliente -->
            <div class="mb-6 p-4 bg-white rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2 border-b pb-2">Cliente</h3>

                <!-- Cliente Existente -->
                <div class="flex items-center mb-4 gap-2">
                    <input type="checkbox" id="cliente_existente_checkbox" class="h-4 w-4">
                    <label for="cliente_existente_checkbox" class="text-sm">Cliente Existente</label>

                    <select id="cliente_existente_select" class="flex-1 border border-gray-300 rounded px-2 py-1" style="display:none">
                        <option value="">Selecione o cliente</option>
                        @foreach($clientes as $c)
                            <option value="{{ $c->id }}"
                                data-nome="{{ $c->nome }}"
                                data-telefone="{{ $c->telefone }}"
                                data-cpf="{{ $c->cpf }}"
                                data-email="{{ $c->email }}"
                                data-logradouro="{{ $c->logradouro }}"
                                data-numero="{{ $c->numero }}"
                                data-complemento="{{ $c->complemento }}"
                                data-bairro="{{ $c->bairro }}"
                                data-cidade="{{ $c->cidade }}"
                                data-estado="{{ $c->estado }}"
                                data-cep="{{ $c->cep }}">
                                {{ $c->nome }} - {{ $c->telefone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-wrap gap-2">
                    <input type="text" name="cliente[nome]" id="cliente_nome" placeholder="Nome do Cliente" required class="flex-1 min-w-[200px] border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cliente[telefone]" id="telefone" placeholder="(13) 99999-9999" required maxlength="15" class="w-40 border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cliente[cpf]" id="cliente_cpf" placeholder="CPF" class="w-40 border border-gray-300 rounded px-2 py-1">
                    <input type="email" name="cliente[email]" id="cliente_email" placeholder="E-mail" class="flex-1 min-w-[200px] border border-gray-300 rounded px-2 py-1">
                </div>

                <div class="flex flex-wrap gap-2 mb-2">
                    <input type="text" name="cliente[logradouro]" id="cliente_logradouro" placeholder="Logradouro (Rua, Av...)" class="flex-1 border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cliente[numero]" id="cliente_numero" placeholder="Número" class="w-24 border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cliente[complemento]" id="cliente_complemento" placeholder="Complemento" class="w-40 border border-gray-300 rounded px-2 py-1">
                </div>

                <div class="flex flex-wrap gap-2 mb-4">
                    <input type="text" name="cliente[bairro]" id="cliente_bairro" placeholder="Bairro" class="flex-1 border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cliente[cidade]" id="cliente_cidade" placeholder="Cidade" class="flex-1 border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cliente[estado]" id="cliente_estado" placeholder="Estado (UF)" maxlength="2" class="w-20 border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cliente[cep]" id="cliente_cep" placeholder="CEP" class="w-32 border border-gray-300 rounded px-2 py-1">
                </div>
            </div>

            <!-- Itens -->
            <div class="mb-6 p-6 bg-white rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Itens</h3>
                <div id="itens" class="mb-4 p-3 border rounded shadow-sm bg-gray-50">
                    <div class="item mb-4">
                        <div class="flex gap-2 mb-2 flex-wrap">
                            <input type="text" name="items[0][nomeItem]" placeholder="Nome do Item" required class="flex-1 min-w-[150px]">
<select name="items[0][material]" class="w-40 material-select" required>
   <option value="">Selecione o tecido</option>
   @foreach(\App\Models\ValorBase::where('tipo','TECIDO')->get() as $v)
      <option value="{{ $v->id }}" data-valor="{{ $v->valor }}">
         {{ $v->nome }} - R$ {{ number_format($v->valor,2,',','.') }}/m²
      </option>
   @endforeach
</select>
                            <input type="number" name="items[0][metragem]" placeholder="Metragem" step="0.01" value="0" class="w-24">
                           <p class="text-sm mt-1">
   <strong>Valor Sugerido:</strong> 
   R$ <span class="valor-sugerido">0,00</span>
</p>

<input type="hidden" name="items[0][valor_sugerido]" class="valor-sugerido-input">

                            <select name="items[0][material_disponib]" required class="w-32">
                                <option value="Pedir" selected>Pedir</option>
                                <option value="Complementar">Complementar</option>
                                <option value="TM">TM</option>
                            </select>
                        </div>
                        
                        <textarea name="items[0][especifi]" placeholder="Especificação / Observações do Item" rows="3"
                                  class="block w-full mb-2 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
                        <button type="button" onclick="removerItem(this)" class="mb-2 bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">Remover Item</button>
                        <br>
                        <h4 class="font-semibold mb-1">Serviços Terceirizados</h4>
                        <div class="terceirizadas-container mb-2" id="terceirizadas-0"></div>
                        <button type="button" onclick="addTerceirizada(0)" class="mb-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded">+ Adicionar Terceirizadas</button>
                        <br><br><hr>
                    </div>
                </div>
                <button type="button" onclick="addItem()" class="mb-4 bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded">+ Adicionar Item</button>
            </div>

            <!-- Imagens do Pedido -->
            <h3 class="text-lg font-semibold mb-2">Imagens do Pedido</h3>
            <input type="file" name="imagens[]" multiple accept="image/*" class="block mb-4">

            <!-- Tapeceiro -->
            <label for="tapeceiro" class="block mb-1">Tapeceiro:</label>
            <select name="tapeceiro" id="tapeceiro" class="block w-full mb-4 border border-gray-300 rounded px-2 py-1">
                <option value="" selected>Distribuir</option>
                @foreach($profissionais as $prof)
                    @if($prof->nome != 'Distribuir')
                        <option value="{{ $prof->id }}">{{ $prof->nome }}</option>
                    @endif
                @endforeach
            </select>

            <!-- Datas e Prazos -->
            <h3 class="text-lg font-semibold mb-2">Datas e Prazos</h3>
            <div class="flex gap-4 mb-4">
                <div class="flex-1">
                    <label>Data de Retirada</label>
                    <input type="date" name="data_retirada" id="data_retirada" class="w-min">
                    <label class="flex items-center gap-1">
                        <input type="radio" name="periodo_retirada" value="Manhã">
                        Manhã
                    </label>
                    <label class="flex items-center gap-1">
                        <input type="radio" name="periodo_retirada" value="Tarde">
                        Tarde
                    </label>
                    <label class="flex items-center gap-1">
                        <input type="radio" name="periodo_retirada" value="Combinar" checked>
                        Combinar
                    </label>
                </div>

                <div class="flex-1">
                    <label>Prazo</label>
                    <input type="date" name="prazo" id="prazo" class="w-min">
                    <span id="prazo_info" class="ml-2 text-gray-600"></span>
                </div>
            </div>

<!-- 🔹 VALOR SUGERIDO DO PEDIDO (SOMA DOS ITENS) -->
<p class="text-sm mt-2 mb-2">
   <strong>Valor Sugerido do Pedido:</strong>  
   R$ <span id="valor-sugerido-total">0,00</span>
</p>

<input type="hidden" name="valor_sugerido_total" id="valor_sugerido_total">

            <!-- Valor Total -->
            <h3 class="text-lg font-semibold mb-2">Valor Total</h3>
            <input type="text" id="valor_total" name="valor" placeholder="R$ 0,00" required class="block w-full mb-4 border border-gray-300 rounded px-2 py-1">

            <!-- Pagamentos -->
            <div class="mb-6 p-6 bg-white rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Pagamentos</h3>
                <div id="pagamentos" class="mb-4">
                    <div class="pagamento mb-2">
                        <input type="text" name="pagamentos[0][valor]" placeholder="R$ 0,00" required class="mb-1 block w-full border border-gray-300 rounded px-2 py-1 valor-mask">
                        <select name="pagamentos[0][forma]" required onchange="toggleDataPagamento(this)" class="mb-1 block w-full border border-gray-300 rounded px-2 py-1">
                            <option value="">Selecione</option>
                            <option value="PIX">PIX</option>
                            <option value="DEBITO">DEBITO</option>
                            <option value="DINHEIRO">DINHEIRO</option>
                            <option value="CREDITO À VISTA">CREDITO À VISTA</option>
                            <option value="CREDITO PARCELADO">CREDITO PARCELADO</option>
                            <option value="BOLETO">BOLETO</option>
                            <option value="CHEQUE">CHEQUE</option>
                            <option value="NA ENTREGA">NA ENTREGA</option>
                            <option value="A PRAZO">A PRAZO</option>
                            <option value="OUTROS">OUTROS</option>
                        </select>
                        <input type="text" name="pagamentos[0][obs]" placeholder="Observação" class="mb-1 block w-full border border-gray-300 rounded px-2 py-1">
                        <input type="date" name="pagamentos[0][data]" style="display:none; margin-top:5px;" class="mb-2 block w-full border border-gray-300 rounded px-2 py-1">
                        <button type="button" onclick="removerPagamento(this)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">Remover</button>
                        <br><br><hr>
                    </div>
                </div>
                <button type="button" onclick="addPagamento()" class="mb-4 bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded">+ Adicionar Pagamento</button>
            </div>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Salvar Pedido</button>
        </form>
    </div>

    <!-- Scripts -->
    <script>
        // Máscara de telefone
        document.addEventListener("DOMContentLoaded", function() {
            const telefoneInput = document.getElementById("telefone");
            telefoneInput.addEventListener("input", function() {
                let value = telefoneInput.value.replace(/\D/g, "");
                if (value.length > 11) value = value.substring(0, 11);
                if (value.length > 6) telefoneInput.value = `(${value.substring(0,2)}) ${value.substring(2,value.length-4)}-${value.substring(value.length-4)}`;
                else if (value.length > 2) telefoneInput.value = `(${value.substring(0,2)}) ${value.substring(2)}`;
                else telefoneInput.value = value;
            });
        });

        let itemIndex = 1;
        let pagamentoIndex = 1;
        let terceirizadaIndex = {0:0};

        function addItem() {
            const wrapper = document.getElementById('itens');
            const newItem = document.createElement('div');
            newItem.classList.add('item','mb-4');
            newItem.innerHTML = `
                <div class="flex gap-2 mb-2 flex-wrap">
                    <input type="text" name="items[${itemIndex}][nomeItem]" placeholder="Nome do Item" class="flex-1 min-w-[150px]">
<select name="items[${itemIndex}][material]" class="w-40 material-select" required>
   <option value="">Selecione o tecido</option>
   @foreach(\App\Models\ValorBase::where('tipo','TECIDO')->get() as $v)
      <option value="{{ $v->id }}" data-valor="{{ $v->valor }}">
         {{ $v->nome }} - R$ {{ number_format($v->valor,2,',','.') }}/m²
      </option>
   @endforeach
</select>
                    <input type="number" name="items[${itemIndex}][metragem]" placeholder="Metragem" step="0.01" value="0" class="w-24">
<p class="text-sm mt-1">
   <strong>Valor Sugerido:</strong> 
   R$ <span class="valor-sugerido">0,00</span>
</p>

                        <input type="hidden" name="items[${itemIndex}][valor_sugerido]" class="valor-sugerido-input">
                        <select name="items[${itemIndex}][material_disponib]" required class="w-32">
                        <option value="Pedir" selected>Pedir</option>
                        <option value="Complementar">Complementar</option>
                        <option value="TM">TM</option>
                    </select>
                </div>
                <textarea name="items[${itemIndex}][especifi]" placeholder="Especificação / Observações do Item" rows="3" class="block w-full mb-2 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
                <button type="button" onclick="removerItem(this)" class="mb-2 bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">Remover Item</button>
                <h4 class="font-semibold mb-1">Serviços Terceirizados</h4>
                <div class="terceirizadas-container mb-2" id="terceirizadas-${itemIndex}"></div>
                <button type="button" onclick="addTerceirizada(${itemIndex})" class="mb-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded">+ Adicionar Terceirizadas</button>
                 <br>
                  <br>
                        <hr>`;
            wrapper.appendChild(newItem);
            terceirizadaIndex[itemIndex] = 0;
            itemIndex++;
        }

     function addTerceirizada(itemIdx) {
    const container = document.getElementById(`terceirizadas-${itemIdx}`);
    const newTerceirizada = document.createElement('div');
    newTerceirizada.classList.add('terceirizada','mb-1');
    newTerceirizada.innerHTML = `
        <input type="text" name="items[${itemIdx}][terceirizadas][${terceirizadaIndex[itemIdx]}][tipo]" 
               placeholder="Tipo de Serviço" 
               list="servicos-terceirizados" 
               required 
               class="mb-1 block w-full border border-gray-300 rounded px-2 py-1">
        <input type="text" name="items[${itemIdx}][terceirizadas][${terceirizadaIndex[itemIdx]}][obs]" 
               placeholder="Observação" 
               class="mb-1 block w-full border border-gray-300 rounded px-2 py-1">
        <button type="button" onclick="removerTerceirizada(this)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded mb-2">Remover</button>
    `;
    container.appendChild(newTerceirizada);
    terceirizadaIndex[itemIdx]++;
}


        function addPagamento() {
            const wrapper = document.getElementById('pagamentos');
            const newPagamento = document.createElement('div');
            newPagamento.classList.add('pagamento','mb-2');
            newPagamento.innerHTML = `
                <input type="number" step="0.01" name="pagamentos[${pagamentoIndex}][valor]" placeholder="Valor" required class="mb-1 block w-full border border-gray-300 rounded px-2 py-1">
                <select name="pagamentos[${pagamentoIndex}][forma]" required onchange="toggleDataPagamento(this)" class="mb-1 block w-full border border-gray-300 rounded px-2 py-1">
                    <option value="">Selecione</option>
                    <option value="PIX">PIX</option>
                    <option value="DEBITO">DEBITO</option>
                    <option value="DINHEIRO">DINHEIRO</option>
                    <option value="CREDITO À VISTA">CREDITO À VISTA</option>
                    <option value="CREDITO PARCELADO">CREDITO PARCELADO</option>
                    <option value="BOLETO">BOLETO</option>
                    <option value="CHEQUE">CHEQUE</option>
                    <option value="NA ENTREGA">NA ENTREGA</option>
                    <option value="A PRAZO">A PRAZO</option>
                    <option value="OUTROS">OUTROS</option>
                </select>
                <input type="text" name="pagamentos[${pagamentoIndex}][obs]" placeholder="Observação" class="mb-1 block w-full border border-gray-300 rounded px-2 py-1">
                <input type="date" name="pagamentos[${pagamentoIndex}][data]" style="display:none; margin-top:5px;" class="mb-2 block w-full border border-gray-300 rounded px-2 py-1">
                <button type="button" onclick="removerPagamento(this)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">Remover</button><br><br><hr>
            `;
            wrapper.appendChild(newPagamento);
            pagamentoIndex++;
        }

        function toggleDataPagamento(select) {
            const pagamentoDiv = select.closest('.pagamento');
            const inputData = pagamentoDiv.querySelector('input[type="date"]');
            const mostrar = ['OUTROS','A PRAZO', 'BOLETO', 'CHEQUE'].includes(select.value);
            inputData.style.display = mostrar ? 'block' : 'none';
            inputData.required = mostrar;
            if(!mostrar) inputData.value='';
        }

function removerItem(btn){ 
   btn.closest('.item').remove(); 
   atualizarValorSugeridoTotal(); 
}
        function removerPagamento(btn){ btn.closest('.pagamento').remove(); }
        function removerTerceirizada(btn){ btn.closest('.terceirizada').remove(); }

        // Atualização automática de prazo
        document.addEventListener("DOMContentLoaded", function() {
            const dataPedido = document.querySelector('input[name="data"]');
            const dataRetirada = document.getElementById('data_retirada');
            const prazo = document.getElementById('prazo');
            const prazoInfo = document.getElementById('prazo_info');

            dataPedido.addEventListener("change", function() {
                if(this.value) {
                    const data = new Date(this.value);
                    data.setDate(data.getDate() + 30);
                    prazo.value = data.toISOString().split('T')[0];
                }
            });

            dataRetirada.addEventListener("change", function() {
                if(this.value) {
                    const retirada = new Date(this.value);
                    const prazoData = new Date(prazo.value);
                    const diffTime = Math.abs(prazoData - retirada);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    prazoInfo.textContent = `(${diffDays} dias após a retirada)`;
                } else prazoInfo.textContent = '';
            });

            prazo.addEventListener("change", function() {
                if(dataRetirada.value) {
                    const retirada = new Date(dataRetirada.value);
                    const prazoData = new Date(this.value);
                    const diffTime = Math.abs(prazoData - retirada);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    prazoInfo.textContent = `(${diffDays} dias após a retirada)`;
                }
            });
        });
        // Função para formatar moeda brasileira
function formatarMoeda(input) {
    let value = input.value.replace(/\D/g, '');
    value = (value / 100).toFixed(2) + '';
    value = value.replace('.', ',');
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    input.value = 'R$ ' + value;
}

// Aplicar máscara no campo do valor total
const valorTotal = document.getElementById('valor_total');
valorTotal.addEventListener('input', function() {
    formatarMoeda(this);
});

// Aplicar máscara nos pagamentos dinamicamente
document.addEventListener("input", function(e) {
    if(e.target.classList.contains('valor-mask')){
        formatarMoeda(e.target);
    }
});

    </script>

    <script>
document.addEventListener("DOMContentLoaded", function() {
    const checkbox = document.getElementById('cliente_existente_checkbox');
    const select = document.getElementById('cliente_existente_select');

    checkbox.addEventListener('change', function() {
        if(this.checked) {
            select.style.display = 'block';
        } else {
            select.style.display = 'none';
            select.value = '';
            // Limpar campos
            ['nome','telefone','cpf','email','logradouro','numero','complemento','bairro','cidade','estado','cep'].forEach(id => {
                document.getElementById('cliente_'+id).value = '';
            });
        }
    });

    select.addEventListener('change', function() {
        const option = select.selectedOptions[0];
        if(option && option.value) {
            ['nome','telefone','cpf','email','logradouro','numero','complemento','bairro','cidade','estado','cep'].forEach(field => {
                document.getElementById('cliente_'+field).value = option.dataset[field] || '';
            });
        } else {
            ['nome','telefone','cpf','email','logradouro','numero','complemento','bairro','cidade','estado','cep'].forEach(field => {
                document.getElementById('cliente_'+field).value = '';
            });
        }
    });
});

document.getElementById('cliente_existente_checkbox').addEventListener('change', function() {
    const select = document.getElementById('cliente_existente_select');
    select.style.display = this.checked ? 'block' : 'none';
});

document.getElementById('cliente_existente_select').addEventListener('change', function() {
    const option = this.selectedOptions[0];
    if (!option || !option.value) return;

    document.querySelector('input[name="cliente[nome]"]').value = option.dataset.nome || '';
    document.querySelector('input[name="cliente[telefone]"]').value = option.dataset.telefone || '';
    document.querySelector('input[name="cliente[cpf]"]').value = option.dataset.cpf || '';
    document.querySelector('input[name="cliente[email]"]').value = option.dataset.email || '';
    document.querySelector('input[name="logradouro"]').value = option.dataset.logradouro || '';
    document.querySelector('input[name="numero"]').value = option.dataset.numero || '';
    document.querySelector('input[name="complemento"]').value = option.dataset.complemento || '';
    document.querySelector('input[name="bairro"]').value = option.dataset.bairro || '';
    document.querySelector('input[name="cidade"]').value = option.dataset.cidade || '';
    document.querySelector('input[name="estado"]').value = option.dataset.estado || '';
    document.querySelector('input[name="cep"]').value = option.dataset.cep || '';
});
function validarFormulario() {
    let erros = [];

    // Data do pedido
    const dataPedido = document.querySelector('input[name="data"]');
    if (!dataPedido.value) erros.push("Data do pedido não pode estar vazia.");

    // Cliente
    const nomeCliente = document.getElementById('cliente_nome');
    const telefoneCliente = document.getElementById('telefone');
    if (!nomeCliente.value) erros.push("Nome do cliente é obrigatório.");
    if (!telefoneCliente.value) erros.push("Telefone do cliente é obrigatório.");

    // Itens
    const itens = document.querySelectorAll('.item');
    if (itens.length === 0) erros.push("Adicione pelo menos um item.");

    itens.forEach((item, idx) => {
        const nomeItem = item.querySelector(`input[name^="items"][name$="[nomeItem]"]`);
        const metragem = item.querySelector(`input[name^="items"][name$="[metragem]"]`);
        const materialDisp = item.querySelector(`select[name^="items"][name$="[material_disponib]"]`);

        if (!nomeItem.value) erros.push(`Item ${idx+1}: Nome é obrigatório.`);
        if (!materialDisp.value) erros.push(`Item ${idx+1}: Selecione a disponibilidade do material.`);
        
        // Terceirizadas
        const terceirizadas = item.querySelectorAll('.terceirizada input[name$="[tipo]"]');
        terceirizadas.forEach((t, tIdx) => {
            if (!t.value) erros.push(`Item ${idx+1}, Terceirizada ${tIdx+1}: Tipo de serviço é obrigatório.`);
        });
    });

    // Pagamentos
    const pagamentos = document.querySelectorAll('.pagamento');
    pagamentos.forEach((p, pIdx) => {
        const valor = p.querySelector('input[name$="[valor]"]');
        const forma = p.querySelector('select[name$="[forma]"]');
        const data = p.querySelector('input[type="date"]');

        if (!valor.value || parseFloat(valor.value.replace(/[R$,.]/g,'')) <= 0) erros.push(`Pagamento ${pIdx+1}: Valor inválido.`);
        if (!forma.value) erros.push(`Pagamento ${pIdx+1}: Selecione a forma.`);
        if (data && data.style.display !== 'none' && !data.value) erros.push(`Pagamento ${pIdx+1}: Informe a data.`);
    });


    // Valor total
    const valorTotal = document.getElementById('valor_total');
    if (!valorTotal.value || parseFloat(valorTotal.value.replace(/[R$,.]/g,'')) <= 0) erros.push("Valor total inválido.");

    // Exibir erros
    if (erros.length > 0) {
        alert("Corrija os seguintes erros:\n\n" + erros.join("\n"));
        return false; // impede o submit
    }

    return true; // envia o formulário
}
document.querySelectorAll('.periodo-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        // Permite marcar apenas um
        if(this.checked){
            document.querySelectorAll('.periodo-checkbox').forEach(cb => {
                if(cb !== this) cb.checked = false;
            });
        }
        // Atualiza hidden com o valor selecionado ou vazio
        const checked = document.querySelector('.periodo-checkbox:checked');
        document.getElementById('periodo_retirada_hidden').value = checked ? checked.value : '';
    });
});

document.addEventListener("input", function(e) {

   const item = e.target.closest(".item");
   if (!item) return;

   if (
      e.target.name.includes("[metragem]") ||
      e.target.classList.contains("material-select")
   ) {
      calcularValorSugerido(item);
      
   }
});

function calcularValorSugerido(item) {

   const metragem = parseFloat(
      item.querySelector('[name*="[metragem]"]').value || 0
   );

   const materialSelect = item.querySelector(".material-select");
   const valorPorMetro = parseFloat(
      materialSelect.selectedOptions[0]?.dataset.valor || 0
   );

   const valorSugerido = metragem * valorPorMetro;

   // Atualiza tela
   item.querySelector(".valor-sugerido").innerText =
      valorSugerido.toFixed(2).replace(".", ",");

   // Salva no hidden para enviar ao Laravel
   item.querySelector(".valor-sugerido-input").value = valorSugerido;

      atualizarValorSugeridoTotal();

}
function atualizarValorSugeridoTotal() {
   let total = 0;

   document.querySelectorAll(".valor-sugerido-input").forEach(input => {
      total += parseFloat(input.value || 0);
   });

   // Atualiza o texto na tela
   document.getElementById("valor-sugerido-total").innerText =
      total.toFixed(2).replace(".", ",");

   // Atualiza hidden para enviar ao Laravel
   document.getElementById("valor_sugerido_total").value = total;
}

</script>
</x-app-layout>
