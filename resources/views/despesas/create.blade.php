<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nova Despesa</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('despesas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Data --}}
            <div>
                <label for="data" class="block font-medium text-gray-700">Data</label>
                <input type="date" name="data" id="data" value="{{ old('data', date('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            </div>

            {{-- Descrição --}}
            <div>
                <label for="descricao" class="block font-medium text-gray-700">Descrição da Nota/Fatura</label>
                <input type="text" name="descricao" id="descricao" value="{{ old('descricao') }}" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            </div>

            {{-- Valor Total --}}
            <div>
                <label for="valor" class="block font-medium text-gray-700">Valor Total</label>
                <input type="number" step="0.01" name="valor" id="valor" value="{{ old('valor') }}" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            </div>

            {{-- Categoria --}}
            <div>
                <label for="categoria" class="block font-medium text-gray-700">Categoria</label>
                <select name="categoria" id="categoria" required class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                    @foreach(['FORNECEDOR', 'AGUA', 'LUZ', 'MATERIAL', 'PARTICULAR', 'OUTROS'] as $cat)
                        <option value="{{ $cat }}" @selected(old('categoria') == $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Produtos --}}
            <div class="flex items-center gap-2 mt-4">
                <input type="checkbox" id="expandirProdutos" class="form-checkbox h-5 w-5 text-blue-600">
                <label for="expandirProdutos" class="text-gray-700 font-medium">Mostrar/ocultar produtos adicionados</label>
            </div>

            <div id="produtos-container" class="space-y-4 mt-2"></div>
            <button type="button" id="add-produto"
                class="bg-purple-600 text-white px-3 py-1 rounded hover:bg-purple-700 mt-2">
                + Novo Produto
            </button>

            {{-- Forma de pagamento --}}
            <div>
                <label for="forma_pagamento" class="block font-medium text-gray-700">Forma de Pagamento da Despesa</label>
                <select name="forma_pagamento" id="forma_pagamento" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                    @foreach(['À VISTA', 'A PRAZO'] as $fp)
                        <option value="{{ $fp }}" @selected(old('forma_pagamento') == $fp)>{{ $fp }}</option>
                    @endforeach
                </select>
            </div>

            <div id="pagamento_avista" class="hidden mt-4">
                <label for="forma_pagamento_avista" class="block font-medium text-gray-700">Forma de Pagamento à Vista</label>
                <select name="forma_pagamento_avista" id="forma_pagamento_avista" class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                    @foreach(['PIX', 'DINHEIRO', 'DÉBITO', 'CRÉDITO', 'OUTROS'] as $fp)
                        <option value="{{ $fp }}">{{ $fp }}</option>
                    @endforeach
                </select>
            </div>

            <div id="pagamento_pendente" class="hidden space-y-4 mt-4">
                <button type="button" id="add-parcela"
                    class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                    + Adicionar Parcela/Boleto
                </button>
                <div id="parcelas-container" class="space-y-4 mt-2"></div>
            </div>

            {{-- Comprovante --}}
            <div>
                <label for="comprovante" class="block font-medium text-gray-700">Comprovante (jpg, png, pdf)</label>
                <input type="file" name="comprovante" id="comprovante" accept=".jpg,.jpeg,.png,.pdf"
                    class="mt-1 block w-full" />
            </div>

            {{-- Observação --}}
            <div>
                <label for="observacao" class="block font-medium text-gray-700">Observação</label>
                <textarea name="observacao" id="observacao" rows="3"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm">{{ old('observacao') }}</textarea>
            </div>

            {{-- Botões --}}
            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Salvar
                </button>
                <a href="{{ route('despesas.index') }}" class="ml-4 text-gray-600 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const selectForma = document.getElementById("forma_pagamento");
    const pendenteFields = document.getElementById("pagamento_pendente");
    const avistaFields = document.getElementById("pagamento_avista");
    const addParcelaBtn = document.getElementById("add-parcela");
    const parcelasContainer = document.getElementById("parcelas-container");
    const descricaoNota = document.getElementById("descricao");

    const produtosContainer = document.getElementById("produtos-container");
    const addProdutoBtn = document.getElementById("add-produto");
    const expandirProdutosCheckbox = document.getElementById("expandirProdutos");
    const produtosList = @json($produtos);

    const formasPagamentoParcela = ['PIX', 'DINHEIRO', 'DÉBITO', 'CRÉDITO', 'TRANSFERÊNCIA', 'BOLETO', 'CHEQUE', 'OUTROS'];

    function toggleFields() {
        if(selectForma.value === "A PRAZO") {
            pendenteFields.classList.remove("hidden");
            avistaFields.classList.add("hidden");
            if(!parcelasContainer.children.length) addParcela();
        } else {
            pendenteFields.classList.add("hidden");
            parcelasContainer.innerHTML = "";
            avistaFields.classList.remove("hidden");
        }
    }

    function addParcela() {
        const index = parcelasContainer.children.length + 1;
        const numero = index.toString().padStart(2,'0');
        const descricaoParcela = `${descricaoNota.value} - ${numero}`;
        const div = document.createElement('div');
        div.classList.add('border','p-3','rounded','bg-gray-50','relative');
        div.innerHTML = `
            <button type="button" class="remove-parcela absolute top-2 right-2 text-red-600 hover:text-red-800 font-bold">X</button>
            <div><label class="block text-sm font-medium">Descrição da Parcela</label><input type="text" name="parcelas_descricao[]" value="${descricaoParcela}" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/></div>
            <div><label class="block text-sm font-medium">Valor</label><input type="number" step="0.01" name="parcelas_valor[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/></div>
            <div><label class="block text-sm font-medium">Data Vencimento</label><input type="date" name="data_vencimento[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/></div>
            <div><label class="block text-sm font-medium">Chave Pagamento</label><input type="text" name="chave_pagamento[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/></div>
            <div><label class="block text-sm font-medium">Forma de Pagamento</label><select name="parcelas_forma_pagamento[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm">${formasPagamentoParcela.map(f=>`<option value="${f}" ${f==='PIX'?'selected':''}>${f}</option>`).join('')}</select></div>
        `;
        parcelasContainer.appendChild(div);
        div.querySelector('.remove-parcela').addEventListener('click',()=>div.remove());
    }

    descricaoNota.addEventListener('input',()=>{
        Array.from(parcelasContainer.children).forEach((div,idx)=>{
            const input = div.querySelector('input[name="parcelas_descricao[]"]');
            if(!input.dataset.userEdited){
                const numero = (idx+1).toString().padStart(2,'0');
                input.value = `${descricaoNota.value} - ${numero}`;
            }
        });
    });

    parcelasContainer.addEventListener('input', e=>{
        if(e.target.name==='parcelas_descricao[]') e.target.dataset.userEdited = true;
    });

    addParcelaBtn.addEventListener('click', addParcela);
    selectForma.addEventListener('change', toggleFields);
    toggleFields();

    // Produtos
    function addProduto() {
        const div = document.createElement('div');
        div.classList.add('border','p-3','rounded','bg-gray-50','relative');
        div.innerHTML = `
            <button type="button" class="remove-produto absolute top-2 right-2 text-red-600 hover:text-red-800 font-bold">X</button>
            <div class="flex items-center gap-2">
                <label class="block text-sm font-medium">Produto</label>
                <select name="produtos_id[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                    <option value="">Selecione o Produto</option>
                    ${produtosList.map(p=>`<option value="${p.id}" data-unidade="${p.unidade_medida}" data-categoria="${p.categoria}">${p.nome}</option>`).join('')}
                </select>
                <button type="button" class="toggle-novo-produto bg-gray-300 px-2 rounded text-sm">Novo</button>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <label class="block text-sm font-medium">Categoria</label>
                <input type="text" name="produtos_categoria[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm" readonly/>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <label class="block text-sm font-medium">Quantidade</label>
                <input type="number" step="0.01" name="produtos_quantidade[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required/>
            </div>
            <div class="flex items-center gap-2 mt-2">
                <label class="block text-sm font-medium">Unidade de Medida</label>
                <input type="text" name="produtos_unidade_medida[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm" readonly/>
            </div>
            <div>
                <label class="block text-sm font-medium">Valor Unitário</label>
                <input type="number" step="0.01" name="produtos_valor_unitario[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required/>
            </div>
            <div>
                <label class="block text-sm font-medium">Valor Total</label>
                <input type="number" step="0.01" name="produtos_valor_total[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm" required/>
            </div>
            <div>
                <label class="block text-sm font-medium">Observação</label>
                <textarea name="produtos_obs[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm"></textarea>
            </div>
        `;

        const selectProduto = div.querySelector('select[name="produtos_id[]"]');
        const categoriaInput = div.querySelector('input[name="produtos_categoria[]"]');
        const unidadeInput = div.querySelector('input[name="produtos_unidade_medida[]"]');
        const toggleBtn = div.querySelector('.toggle-novo-produto');

        selectProduto.addEventListener('change', function(){
            const opt = this.options[this.selectedIndex];
            categoriaInput.value = opt.dataset.categoria || '';
            unidadeInput.value = opt.dataset.unidade || '';
            unidadeInput.readOnly = true;
        });

        toggleBtn.addEventListener('click', function(){
            const inputNovo = document.createElement('input');
            inputNovo.type = 'text';
            inputNovo.name = 'produtos_novo[]';
            inputNovo.className = selectProduto.className;
            inputNovo.placeholder = 'Digite o nome do novo produto';
            selectProduto.replaceWith(inputNovo);
            categoriaInput.readOnly = false;
            unidadeInput.readOnly = false;
            toggleBtn.textContent = 'Seleção';
        });

        produtosContainer.appendChild(div);
    }

    addProdutoBtn.addEventListener('click', addProduto);
    produtosContainer.addEventListener('click', e=>{
        if(e.target.classList.contains('remove-produto')){
            e.target.closest('div.border').remove();
        }
    });

    // Expandir produtos apenas mostra/esconde o container
    expandirProdutosCheckbox.addEventListener('change', ()=>{
        produtosContainer.style.display = expandirProdutosCheckbox.checked ? 'block' : 'none';
    });
});
</script>
