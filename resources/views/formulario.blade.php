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

            <h3 class="text-lg font-semibold mb-2">Cliente</h3>
            <input type="text" name="cliente[nome]" placeholder="Nome do Cliente" required class="block w-full mb-2">
            <input type="text" name="cliente[telefone]" placeholder="Telefone" required class="block w-full mb-2">
            <input type="text" name="cliente[endereco]" placeholder="Endereço" required class="block w-full mb-2">
            <input type="text" name="cliente[cpf]" placeholder="CPF" required class="block w-full mb-2">
            <input type="email" name="cliente[email]" placeholder="E-mail" required class="block w-full mb-4">

            <h3 class="text-lg font-semibold mb-2">Datas e Prazos</h3>
            <div class="mb-4">
                <label>Data do Pedido</label>
                <input type="date" name="data" required class="block w-full">
            </div>
            <div class="mb-4">
                <label>Data de Retirada</label>
                <input type="date" name="data_retirada" class="block w-full">
            </div>
            <div class="mb-4">
                <label>Prazo</label>
                <input type="date" name="prazo" class="block w-full">
            </div>

            <h3 class="text-lg font-semibold mb-2">Itens</h3>
            <div id="itens">
                <div class="item mb-4">
                    <input type="text" name="items[0][nomeItem]" placeholder="Nome do Item" required class="block w-full mb-2">
                    <input type="text" name="items[0][material]" placeholder="Material" required class="block w-full mb-2">
                    <input type="number" name="items[0][metragem]" placeholder="Metragem" step="0.01" required class="block w-full mb-2">
                    <select name="items[0][material_disponib]" required class="block w-full mb-2">
                        <option value="Pedir" selected>Pedir</option>
                        <option value="Complementar">Complementar</option>
                        <option value="TM">TM</option>
                    </select>
                    <input type="text" name="items[0][especifi]" placeholder="Especificação" class="block w-full mb-2">
                    <button type="button" onclick="removerItem(this)" class="mb-2 bg-red-500 text-white px-2 py-1 rounded">Remover Item</button>

                    <h4 class="font-semibold mb-1">Serviços Terceirizados</h4>
                    <div class="terceirizadas-container mb-2" id="terceirizadas-0"></div>
                    <button type="button" onclick="addTerceirizada(0)" class="mb-2 bg-blue-500 text-white px-2 py-1 rounded">+ Adicionar Terceirizadas</button>
                </div>
            </div>
            <button type="button" onclick="addItem()" class="mb-4 bg-green-500 text-white px-3 py-1 rounded">+ Adicionar Item</button>

            <h3 class="text-lg font-semibold mb-2">Imagens do Pedido</h3>
            <input type="file" name="imagens[]" multiple accept="image/*" class="block mb-4">

            <label for="tapeceiro" class="block mb-1">Tapeceiro:</label>
            <select name="tapeceiro" id="tapeceiro" class="block w-full mb-4">
                <option value="">Distribuir</option>
                @foreach($profissionais as $prof)
                    <option value="{{ $prof->id }}">{{ $prof->nome }}</option>
                @endforeach
            </select>

            <h3 class="text-lg font-semibold mb-2">Resumo Final</h3>
            <input type="number" step="0.01" name="valor" placeholder="Valor Total" required class="block w-full mb-4">

            <h3 class="text-lg font-semibold mb-2">Pagamentos</h3>
            <div id="pagamentos" class="mb-4">
                <div class="pagamento mb-2">
                    <input type="number" step="0.01" name="pagamentos[0][valor]" placeholder="Valor" required class="mb-1 block w-full">
                    <select name="pagamentos[0][forma]" required onchange="toggleDataPagamento(this)" class="mb-1 block w-full">
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
                    <input type="text" name="pagamentos[0][obs]" placeholder="Observação" class="mb-1 block w-full">
                    <input type="date" name="pagamentos[0][data]" style="display:none; margin-top:5px;" class="mb-2 block w-full">
                    <button type="button" onclick="removerPagamento(this)" class="bg-red-500 text-white px-2 py-1 rounded">Remover</button>
                </div>
            </div>
            <button type="button" onclick="addPagamento()" class="mb-4 bg-green-500 text-white px-3 py-1 rounded">+ Adicionar Pagamento</button>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar Pedido</button>
        </form>
    </div>

    <script>
        let itemIndex = 1;
        let pagamentoIndex = 1;
        let terceirizadaIndex = {0:0};

        function addItem() {
            const wrapper = document.getElementById('itens');
            const newItem = document.createElement('div');
            newItem.classList.add('item', 'mb-4');
            newItem.innerHTML = `
                <input type="text" name="items[${itemIndex}][nomeItem]" placeholder="Nome do Item" required class="block w-full mb-2">
                <input type="text" name="items[${itemIndex}][material]" placeholder="Material" required class="block w-full mb-2">
                <input type="number" name="items[${itemIndex}][metragem]" placeholder="Metragem" step="0.01" required class="block w-full mb-2">
                <select name="items[${itemIndex}][material_disponib]" required class="block w-full mb-2">
                    <option value="Pedir" selected>Pedir</option>
                    <option value="Complementar">Complementar</option>
                    <option value="TM">TM</option>
                </select>
                <input type="text" name="items[${itemIndex}][especifi]" placeholder="Especificação" class="block w-full mb-2">
                <button type="button" onclick="removerItem(this)" class="mb-2 bg-red-500 text-white px-2 py-1 rounded">Remover Item</button>
                <h4 class="font-semibold mb-1">Serviços Terceirizados</h4>
                <div class="terceirizadas-container mb-2" id="terceirizadas-${itemIndex}"></div>
                <button type="button" onclick="addTerceirizada(${itemIndex})" class="mb-2 bg-blue-500 text-white px-2 py-1 rounded">+ Adicionar Terceirizadas</button>
            `;
            wrapper.appendChild(newItem);
            terceirizadaIndex[itemIndex] = 0;
            itemIndex++;
        }

        function addPagamento() {
            const wrapper = document.getElementById('pagamentos');
            const newPagamento = document.createElement('div');
            newPagamento.classList.add('pagamento', 'mb-2');
            newPagamento.innerHTML = `
                <input type="number" step="0.01" name="pagamentos[${pagamentoIndex}][valor]" placeholder="Valor" required class="mb-1 block w-full">
                <select name="pagamentos[${pagamentoIndex}][forma]" required onchange="toggleDataPagamento(this)" class="mb-1 block w-full">
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
                <input type="text" name="pagamentos[${pagamentoIndex}][obs]" placeholder="Observação" class="mb-1 block w-full">
                <input type="date" name="pagamentos[${pagamentoIndex}][data]" style="display:none; margin-top:5px;" class="mb-2 block w-full">
                <button type="button" onclick="removerPagamento(this)" class="bg-red-500 text-white px-2 py-1 rounded">Remover</button>
            `;
            wrapper.appendChild(newPagamento);
            pagamentoIndex++;
        }

        function toggleDataPagamento(select) {
            const pagamentoDiv = select.closest('.pagamento');
            const inputData = pagamentoDiv.querySelector('input[type="date"]');
            const mostrar = ['OUTROS', 'A PRAZO'].includes(select.value);
            inputData.style.display = mostrar ? 'block' : 'none';
            inputData.required = mostrar;
            if(!mostrar) inputData.value='';
        }

        function addTerceirizada(itemIdx){
            const container = document.getElementById(`terceirizadas-${itemIdx}`);
            const newTerceirizada = document.createElement('div');
            newTerceirizada.classList.add('terceirizada', 'mb-1');
            newTerceirizada.innerHTML = `
                <input type="text" name="items[${itemIdx}][terceirizadas][${terceirizadaIndex[itemIdx]}][tipo]" placeholder="Tipo de Serviço" required class="mb-1 block w-full">
                <input type="text" name="items[${itemIdx}][terceirizadas][${terceirizadaIndex[itemIdx]}][obs]" placeholder="Observação" class="mb-1 block w-full">
                <button type="button" onclick="removerTerceirizada(this)" class="bg-red-500 text-white px-2 py-1 rounded mb-2">Remover</button>
            `;
            container.appendChild(newTerceirizada);
            terceirizadaIndex[itemIdx]++;
        }

        function removerItem(btn){ btn.closest('.item').remove(); }
        function removerPagamento(btn){ btn.closest('.pagamento').remove(); }
        function removerTerceirizada(btn){ btn.closest('.terceirizada').remove(); }
    </script>
</x-app-layout>
