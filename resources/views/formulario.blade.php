<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Formulário de Pedido</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <form action="{{ route('pedidos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Data do Pedido -->
            <div class="flex-1 mb-4">
                <label>Data do Pedido</label>
                <input type="date" name="data" class="w-min border border-gray-300 rounded px-2 py-1">
            </div>

            <!-- Cliente -->
            <div class="mb-6 p-4 bg-white rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-2 border-b pb-2">Cliente</h3>
                <div class="flex flex-wrap gap-2">
                    <input type="text" name="cliente[nome]" placeholder="Nome do Cliente" required class="flex-1 min-w-[200px] border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cliente[telefone]" id="telefone" placeholder="(13) 99999-9999" required maxlength="15" class="w-40 border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cliente[cpf]" placeholder="CPF" class="w-40 border border-gray-300 rounded px-2 py-1">
                    <input type="email" name="cliente[email]" placeholder="E-mail" class="flex-1 min-w-[200px] border border-gray-300 rounded px-2 py-1">
                </div>
                <div class="flex flex-wrap gap-2 mb-2">
                    <input type="text" name="logradouro" placeholder="Logradouro (Rua, Av...)" class="flex-1 min-w-[150px] border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="numero" placeholder="Número" class="w-32 border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="complemento" placeholder="Complemento (Apto, Bloco)" class="w-40 border border-gray-300 rounded px-2 py-1">
                </div>
                <div class="flex flex-wrap gap-2 mb-4">
                    <input type="text" name="bairro" placeholder="Bairro" class="flex-1 min-w-[150px] border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cidade" placeholder="Cidade" class="flex-1 min-w-[150px] border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="estado" placeholder="Estado (UF)" maxlength="2" class="w-20 border border-gray-300 rounded px-2 py-1">
                    <input type="text" name="cep" placeholder="CEP" class="w-28 border border-gray-300 rounded px-2 py-1">
                </div>
            </div>

            <!-- Itens -->
            <div class="mb-6 p-6 bg-white rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Itens</h3>
                <div id="itens" class="mb-4 p-3 border rounded shadow-sm bg-gray-50">
                    <div class="item mb-4">
                        <div class="flex gap-2 mb-2 flex-wrap">
                            <input type="text" name="items[0][nomeItem]" placeholder="Nome do Item" class="flex-1 min-w-[150px]">
                            <input type="text" name="items[0][material]" placeholder="Material" class="w-40">
                            <input type="number" name="items[0][metragem]" placeholder="Metragem" step="0.01" class="w-24">
                            <select name="items[0][material_disponib]" required class="w-32">
                                <option value="Pedir" selected>Pedir</option>
                                <option value="Complementar">Complementar</option>
                                <option value="TM">TM</option>
                            </select>
                        </div>
                        <textarea name="items[0][especifi]" placeholder="Especificação / Observações do Item" rows="3"
                                  class="block w-full mb-2 border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-200"></textarea>
                        <button type="button" onclick="removerItem(this)" class="mb-2 bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">Remover Item</button>
                        <h4 class="font-semibold mb-1">Serviços Terceirizados</h4>
                        <div class="terceirizadas-container mb-2" id="terceirizadas-0"></div>
                        <button type="button" onclick="addTerceirizada(0)" class="mb-2 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded">+ Adicionar Terceirizadas</button>
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
                </div>
                <div class="flex-1">
                    <label>Prazo</label>
                    <input type="date" name="prazo" id="prazo" class="w-min">
                    <span id="prazo_info" class="ml-2 text-gray-600"></span>
                </div>
            </div>

            <!-- Valor Total -->
            <h3 class="text-lg font-semibold mb-2">Valor Total</h3>
            <input type="number" step="0.01" name="valor" placeholder="Valor Total" required class="block w-full mb-4 border border-gray-300 rounded px-2 py-1">

            <!-- Pagamentos -->
            <div class="mb-6 p-6 bg-white rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4 border-b pb-2">Pagamentos</h3>
                <div id="pagamentos" class="mb-4">
                    <div class="pagamento mb-2">
                        <input type="number" step="0.01" name="pagamentos[0][valor]" placeholder="Valor" required class="mb-1 block w-full border border-gray-300 rounded px-2 py-1">
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
                    <input type="text" name="items[${itemIndex}][material]" placeholder="Material" class="w-40">
                    <input type="number" name="items[${itemIndex}][metragem]" placeholder="Metragem" step="0.01" class="w-24">
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
            `;
            wrapper.appendChild(newItem);
            terceirizadaIndex[itemIndex] = 0;
            itemIndex++;
        }

        function addTerceirizada(itemIdx) {
            const container = document.getElementById(`terceirizadas-${itemIdx}`);
            const newTerceirizada = document.createElement('div');
            newTerceirizada.classList.add('terceirizada','mb-1');
            newTerceirizada.innerHTML = `
                <input type="text" name="items[${itemIdx}][terceirizadas][${terceirizadaIndex[itemIdx]}][tipo]" placeholder="Tipo de Serviço" required class="mb-1 block w-full border border-gray-300 rounded px-2 py-1">
                <input type="text" name="items[${itemIdx}][terceirizadas][${terceirizadaIndex[itemIdx]}][obs]" placeholder="Observação" class="mb-1 block w-full border border-gray-300 rounded px-2 py-1">
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
                <button type="button" onclick="removerPagamento(this)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded">Remover</button>
            `;
            wrapper.appendChild(newPagamento);
            pagamentoIndex++;
        }

        function toggleDataPagamento(select) {
            const pagamentoDiv = select.closest('.pagamento');
            const inputData = pagamentoDiv.querySelector('input[type="date"]');
            const mostrar = ['OUTROS','A PRAZO'].includes(select.value);
            inputData.style.display = mostrar ? 'block' : 'none';
            inputData.required = mostrar;
            if(!mostrar) inputData.value='';
        }

        function removerItem(btn){ btn.closest('.item').remove(); }
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
    </script>
</x-app-layout>
