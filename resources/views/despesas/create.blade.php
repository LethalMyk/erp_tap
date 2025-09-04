<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nova Despesa</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4">
        {{-- Erros de validação --}}
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

            {{-- Data do Registro --}}
            <div>
                <label for="data" class="block font-medium text-gray-700">Data</label>
                <input type="date" name="data" id="data" value="{{ old('data', date('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            </div>

            {{-- Descrição da Nota/Fatura --}}
            <div>
                <label for="descricao" class="block font-medium text-gray-700">Descrição da Nota/Fatura</label>
                <input type="text" name="descricao" id="descricao" value="{{ old('descricao') }}" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            </div>

            {{-- Valor Total da Nota --}}
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

            {{-- Forma de Pagamento --}}
            <div>
                <label for="forma_pagamento" class="block font-medium text-gray-700">Forma Pagamento</label>
                <select name="forma_pagamento" id="forma_pagamento" required class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                    @foreach(['PIX', 'DINHEIRO', 'DÉBITO', 'CRÉDITO', 'TRANSFERÊNCIA', 'BOLETO', 'A PRAZO', 'CHEQUE', 'OUTROS'] as $fp)
                        <option value="{{ $fp }}" @selected(old('forma_pagamento') == $fp)>{{ $fp }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Parcelas --}}
            <div id="pagamento_pendente" class="hidden space-y-4 mt-4">
                <button type="button" id="add-parcela"
                    class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                    + Adicionar Parcela/Boleto
                </button>

                <div id="parcelas-container" class="space-y-4 mt-2">
                    {{-- Parcelas carregadas automaticamente --}}
                </div>
            </div>

            {{-- Comprovante --}}
            <div>
                <label for="comprovante" class="block font-medium text-gray-700">Comprovante (jpg, png, pdf)</label>
                <input type="file" name="comprovante" id="comprovante" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full" />
            </div>

            {{-- Observação --}}
            <div>
                <label for="observacao" class="block font-medium text-gray-700">Observação</label>
                <textarea name="observacao" id="observacao" rows="3" class="mt-1 block w-full rounded border-gray-300 shadow-sm">{{ old('observacao') }}</textarea>
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
    const addParcelaBtn = document.getElementById("add-parcela");
    const parcelasContainer = document.getElementById("parcelas-container");
    const descricaoNota = document.getElementById("descricao");

    function toggleFields() {
        const forma = selectForma.value;
        if (["BOLETO", "A PRAZO", "CHEQUE", "OUTROS"].includes(forma)) {
            pendenteFields.classList.remove("hidden");
            if (parcelasContainer.children.length === 0) {
                addParcela(); // cria a primeira parcela
            }
        } else {
            pendenteFields.classList.add("hidden");
            parcelasContainer.innerHTML = "";
        }
    }

    function addParcela() {
        const index = parcelasContainer.children.length + 1;
        const numero = index.toString().padStart(2, '0');
        const descricaoParcela = `${descricaoNota.value} - ${numero}`;

        const parcela = document.createElement("div");
        parcela.classList.add("border", "p-3", "rounded", "bg-gray-50", "relative");

        parcela.innerHTML = `
            <button type="button" class="remove-parcela absolute top-2 right-2 text-red-600 hover:text-red-800 font-bold">X</button>
            <div>
                <label class="block text-sm font-medium">Descrição da Parcela</label>
                <input type="text" name="parcelas_descricao[]" value="${descricaoParcela}" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/>
            </div>
            <div>
                <label class="block text-sm font-medium">Valor</label>
                <input type="number" step="0.01" name="parcelas_valor[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/>
            </div>
            <div>
                <label class="block text-sm font-medium">Data Vencimento</label>
                <input type="date" name="data_vencimento[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/>
            </div>
            <div>
                <label class="block text-sm font-medium">Chave Pagamento</label>
                <input type="text" name="chave_pagamento[]" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/>
            </div>
        `;

        parcelasContainer.appendChild(parcela);
    }

    // Atualiza descrição das parcelas existentes se a nota mudar
    descricaoNota.addEventListener("input", function () {
        Array.from(parcelasContainer.children).forEach((parcelaDiv, idx) => {
            const numero = (idx + 1).toString().padStart(2, '0');
            const input = parcelaDiv.querySelector('input[name="parcelas_descricao[]"]');
            // Só atualiza se o usuário não alterou manualmente
            if (!input.dataset.userEdited) {
                input.value = `${descricaoNota.value} - ${numero}`;
            }
        });
    });

    // Marca o input como editado se o usuário alterar manualmente
    parcelasContainer.addEventListener("input", function(e) {
        if (e.target.name === "parcelas_descricao[]") {
            e.target.dataset.userEdited = true;
        }
    });

    // Remover parcela ao clicar no botão X
    parcelasContainer.addEventListener("click", function(e) {
        if (e.target.classList.contains("remove-parcela")) {
            const parcelaDiv = e.target.closest("div.border");
            parcelaDiv.remove();
            // Atualiza as descrições restantes
            Array.from(parcelasContainer.children).forEach((parcelaDiv, idx) => {
                const numero = (idx + 1).toString().padStart(2, '0');
                const input = parcelaDiv.querySelector('input[name="parcelas_descricao[]"]');
                if (!input.dataset.userEdited) {
                    input.value = `${descricaoNota.value} - ${numero}`;
                }
            });
        }
    });

    addParcelaBtn.addEventListener("click", addParcela);
    selectForma.addEventListener("change", toggleFields);

    toggleFields();
});
</script>
