<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">Novo Orçamento</h2></x-slot>

    <div class="max-w-5xl mx-auto p-6 bg-white rounded-lg shadow">
        <form id="form-orcamento" action="{{ route('orcamentos.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label>Cliente</label>
                <input type="text" name="cliente_nome" class="w-full border p-2" required>
            </div>
            <div class="mb-4 flex gap-4">
                <input type="text" name="telefone" placeholder="Telefone" class="border p-2 w-1/2">
                <input type="email" name="email" placeholder="Email" class="border p-2 w-1/2">
            </div>

            <div id="itens-container"></div>

            <div class="flex gap-3">
                <button type="button" id="add-item" class="bg-green-600 text-white px-3 py-2 rounded">+ Adicionar Item</button>
                <div class="ml-auto text-lg">
                    Total: <span id="total-geral">R$ 0,00</span>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Salvar Orçamento</button>
            </div>
        </form>
    </div>

<script>
    const valoresBase = {
        itens: @json($itens->keyBy('nome')->mapWithKeys(fn($v,$k)=>[$k=>$v->valor])),
        tecidos: @json($tecidos->keyBy('nome')->mapWithKeys(fn($v,$k)=>[$k=>$v->valor])),
        espumas: @json($espumas->keyBy('nome')->mapWithKeys(fn($v,$k)=>[$k=>$v->valor])),
        enchimentos: @json($enchimentos->keyBy('nome')->mapWithKeys(fn($v,$k)=>[$k=>$v->valor])),
        adicionais: @json($adicionais->keyBy('nome')->mapWithKeys(fn($v,$k)=>[$k=>$v->valor])),
    };

    function formatBR(valor) {
        return 'R$ ' + Number(valor).toFixed(2).replace('.',',');
    }

    function calcularLinha(el) {
        const idx = el.dataset.index;
        const selectItem = el.querySelector(`[name="itens[${idx}][item]"]`);
        const selectTecido = el.querySelector(`[name="itens[${idx}][tecido]"]`);
        const qtdInput = el.querySelector(`[name="itens[${idx}][quantidade]"]`);

        let valorBase = valoresBase.itens[selectItem.value] ?? 0;
        let valorTecido = valoresBase.tecidos[selectTecido.value] ?? 0;
        let valorOpcionais = 0;

        el.querySelectorAll(`input[name="itens[${idx}][espumas][]"]:checked`).forEach(cb => {
            valorOpcionais += Number(valoresBase.espumas[cb.value] ?? 0);
        });
        el.querySelectorAll(`input[name="itens[${idx}][enchimentos][]"]:checked`).forEach(cb => {
            valorOpcionais += Number(valoresBase.enchimentos[cb.value] ?? 0);
        });
        el.querySelectorAll(`input[name="itens[${idx}][adicionais][]"]:checked`).forEach(cb => {
            valorOpcionais += Number(valoresBase.adicionais[cb.value] ?? 0);
        });

        const quantidade = Math.max(1, parseInt(qtdInput.value || 1));
        const valorUnitario = Number(valorBase) + Number(valorTecido) + Number(valorOpcionais);
        const subtotal = valorUnitario * quantidade;

        el.querySelector('.valor-unitario').innerText = formatBR(valorUnitario);
        el.querySelector('.subtotal').innerText = formatBR(subtotal);

        atualizarTotalGeral();
    }

    function atualizarTotalGeral() {
        let total = 0;
        document.querySelectorAll('.item-orc').forEach(el => {
            const text = el.querySelector('.subtotal').innerText.replace('R$','').replace('.','').replace(',','.');
            const num = Number(text.replace(',','.')) || 0;
            // text parsing is ugly but works; better to store data- attributes in production
            total += num;
        });
        document.getElementById('total-geral').innerText = formatBR(total);
    }

    function montarSelectOptions(list) {
        return Object.keys(list).map(k => `<option value="${k}">${k}</option>`).join('');
    }

    document.getElementById('add-item').addEventListener('click', () => {
        const container = document.getElementById('itens-container');
        const idx = container.children.length;
        const div = document.createElement('div');
        div.className = 'item-orc border p-4 rounded mb-4';
        div.dataset.index = idx;

        div.innerHTML = `
            <div class="flex gap-4">
                <div class="w-1/3">
                    <label>Item</label>
                    <select name="itens[${idx}][item]" class="w-full border p-2 select-item">${montarSelectOptions(valoresBase.itens)}</select>
                </div>
                <div class="w-1/3">
                    <label>Tecido</label>
                    <select name="itens[${idx}][tecido]" class="w-full border p-2 select-tecido">
                        <option value="">-- nenhum --</option>
                        ${montarSelectOptions(valoresBase.tecidos)}
                    </select>
                </div>
                <div class="w-1/6">
                    <label>Quantidade</label>
                    <input type="number" name="itens[${idx}][quantidade]" value="1" min="1" class="border p-2 quantidade">
                </div>
                <div class="w-1/6 text-right">
                    <label>Valor unit.</label>
                    <div class="valor-unitario">R$ 0,00</div>
                    <div class="subtotal">R$ 0,00</div>
                </div>
            </div>

            <div class="mt-3 grid grid-cols-3 gap-4">
                <div>
                    <strong>Espumas</strong>
                    ${Object.keys(valoresBase.espumas).map(n => `<div><label><input type="checkbox" name="itens[${idx}][espumas][]" value="${n}"> ${n}</label></div>`).join('')}
                </div>
                <div>
                    <strong>Enchimentos</strong>
                    ${Object.keys(valoresBase.enchimentos).map(n => `<div><label><input type="checkbox" name="itens[${idx}][enchimentos][]" value="${n}"> ${n}</label></div>`).join('')}
                </div>
                <div>
                    <strong>Adicionais</strong>
                    ${Object.keys(valoresBase.adicionais).map(n => `<div><label><input type="checkbox" name="itens[${idx}][adicionais][]" value="${n}"> ${n}</label></div>`).join('')}
                </div>
            </div>
        `;

        // eventos para recalcular
        div.querySelectorAll('select, input[type="checkbox"], input[type="number"]').forEach(el => {
            el.addEventListener('change', () => calcularLinha(div));
        });

        container.appendChild(div);
        calcularLinha(div);
    });

    // adicionar um item inicial
    document.getElementById('add-item').click();

    // before submit: serialize subtotals properly handled by names already
    document.getElementById('form-orcamento').addEventListener('submit', function(e){
        // opcional: você pode validar se há pelo menos 1 item etc.
    });
</script>
</x-app-layout>
