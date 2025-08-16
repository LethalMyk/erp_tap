<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista Completa de Despesas</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('despesas.create') }}" 
               class="bg-blue-700 text-white font-semibold px-5 py-3 rounded-lg shadow border border-blue-900 hover:bg-blue-800 transition">
                Nova Despesa
            </a>
            <label class="flex items-center gap-2 text-sm font-medium">
                <input type="checkbox" id="toggle-all-parcelas" class="h-4 w-4">
                Exibir todas as parcelas
            </label>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Separador</th>
                        <th class="px-4 py-2 text-left">Descrição</th>
                        <th class="px-4 py-2 text-left">Valor</th>
                        <th class="px-4 py-2 text-left">Data Vencimento</th>
                        <th class="px-4 py-2 text-left">Data Pagamento</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Categoria</th>
                        <th class="px-4 py-2 text-left">Forma Pagamento</th>
                        <th class="px-4 py-2 text-left">Chave Pagamento</th>
                        <th class="px-4 py-2 text-left">Comprovante</th>
                        <th class="px-4 py-2 text-left">Observação</th>
                        <th class="px-4 py-2 text-left">Criado Por</th>
                        <th class="px-4 py-2 text-left">Criado em</th>
                        <th class="px-4 py-2 text-left">Atualizado em</th>
                        <th class="px-4 py-2 text-left">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($despesas as $despesa)
                        <tr class="bg-gray-100 font-semibold cursor-pointer despesa-row" data-despesa-id="{{ $despesa->id }}">
                            <td class="px-4 py-2">{{ $despesa->id }}</td>
                            <td class="px-4 py-2">{{ $despesa->separador ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $despesa->descricao }}</td>
                            <td class="px-4 py-2">R$ {{ number_format($despesa->valor_total, 2, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $despesa->data_vencimento ? \Carbon\Carbon::parse($despesa->data_vencimento)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-2">{{ $despesa->data_pagamento ? \Carbon\Carbon::parse($despesa->data_pagamento)->format('d/m/Y') : '-' }}</td>
                            <td class="px-4 py-2">{{ $despesa->status }}</td>
                            <td class="px-4 py-2">{{ $despesa->categoria }}</td>
                            <td class="px-4 py-2">{{ $despesa->forma_pagamento }}</td>
                            <td class="px-4 py-2">{{ $despesa->chave_pagamento ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @if($despesa->comprovante)
                                    <a href="{{ asset('storage/'.$despesa->comprovante) }}" target="_blank" class="text-blue-600 underline">Ver</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2">{{ $despesa->observacao ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $despesa->usuario->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $despesa->created_at ? \Carbon\Carbon::parse($despesa->created_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-4 py-2">{{ $despesa->updated_at ? \Carbon\Carbon::parse($despesa->updated_at)->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-4 py-2 flex gap-2">

                                @php
                                    $despesaParaJS = $despesa->toArray();
                                    $despesaParaJS['data_vencimento'] = $despesa->data_vencimento ? \Carbon\Carbon::parse($despesa->data_vencimento)->format('Y-m-d') : null;
                                    $despesaParaJS['data_pagamento'] = $despesa->data_pagamento ? \Carbon\Carbon::parse($despesa->data_pagamento)->format('Y-m-d') : null;
                                    $despesaParaJS['action'] = route('despesas.update', $despesa->id);
                                @endphp

                                <button 
                                    class="bg-yellow-600 text-black font-semibold px-4 py-2 rounded-lg shadow border border-yellow-800 hover:bg-yellow-700 transition"
                                    data-despesa='@json($despesaParaJS)'>
                                    Editar
                                </button>

                                <form action="{{ route('despesas.destroy', $despesa->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta despesa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white font-semibold px-4 py-2 rounded-lg shadow border border-red-800 hover:bg-red-700 transition">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>

                        {{-- Parcelas --}}
                        @foreach($despesa->parcelas as $parcela)
                            <tr class="bg-white hidden parcela-{{ $despesa->id }}" data-parcela-id="{{ $parcela->id }}">
                                <td class="px-4 py-2">↳ {{ $parcela->id }}</td>
                                <td class="px-4 py-2">{{ $parcela->separador ?? '-' }}</td>
                                <td class="px-4 py-2 pl-6">↳ {{ $parcela->descricao }}</td>
                                <td class="px-4 py-2">R$ {{ number_format($parcela->valor_parcela, 2, ',', '.') }}</td>
                                <td class="px-4 py-2">{{ $parcela->data_vencimento ? \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-2 parcela-pagamento">{{ $parcela->data_pagamento ? \Carbon\Carbon::parse($parcela->data_pagamento)->format('d/m/Y') : '-' }}</td>
                                <td class="px-4 py-2 parcela-status">{{ $parcela->status }}</td>
                                <td class="px-4 py-2">{{ $parcela->categoria }}</td>
                                <td class="px-4 py-2">{{ $parcela->forma_pagamento }}</td>
                                <td class="px-4 py-2">{{ $parcela->chave_pagamento ?? '-' }}</td>
                                <td class="px-4 py-2">
                                    @if($parcela->comprovante)
                                        <a href="{{ asset('storage/'.$parcela->comprovante) }}" target="_blank" class="text-blue-600 underline">Ver</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-2">{{ $parcela->observacao ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $parcela->usuario->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $parcela->created_at ? \Carbon\Carbon::parse($parcela->created_at)->format('d/m/Y H:i') : '-' }}</td>
                                <td class="px-4 py-2">{{ $parcela->updated_at ? \Carbon\Carbon::parse($parcela->updated_at)->format('d/m/Y H:i') : '-' }}</td>
                                <td class="px-4 py-2 flex gap-2">
                                    @php
                                        $parcelaParaJS = $parcela->toArray();
                                        $parcelaParaJS['data_vencimento'] = $parcela->data_vencimento ? \Carbon\Carbon::parse($parcela->data_vencimento)->format('Y-m-d') : null;
                                        $parcelaParaJS['data_pagamento'] = $parcela->data_pagamento ? \Carbon\Carbon::parse($parcela->data_pagamento)->format('Y-m-d') : null;
                                        $parcelaParaJS['action'] = route('despesas.update', $parcela->id);
                                    @endphp

                                    <button 
                                        class="bg-yellow-600 text-black font-semibold px-4 py-2 rounded-lg shadow border border-yellow-800 hover:bg-yellow-700 transition"
                                        data-parcela='@json($parcelaParaJS)'>
                                        Editar
                                    </button>

                                    <form action="{{ route('despesas.destroy', $parcela->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta parcela?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white font-semibold px-4 py-2 rounded-lg shadow border border-red-800 hover:bg-red-700 transition">
                                            Excluir
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $despesas->links() }}
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div id="editParcelaModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96 relative">
            <button id="closeModal" class="absolute top-2 right-2 text-gray-600 font-bold hover:text-gray-800">X</button>
            <h3 class="text-lg font-semibold mb-4">Editar Parcela / Registrar Pagamento</h3>
            <form id="editParcelaForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="mb-2">
                    <label class="block text-sm font-medium">Descrição</label>
                    <input type="text" name="descricao" id="modal_descricao" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium">Valor</label>
                    <input type="number" step="0.01" name="valor" id="modal_valor" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium">Data Vencimento</label>
                    <input type="date" name="data_vencimento" id="modal_vencimento" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/>
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium">Data Pagamento</label>
                    <input type="date" name="data_pagamento" id="modal_pagamento" class="mt-1 block w-full rounded border-gray-300 shadow-sm"/>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium">Comprovante</label>
                    <input type="file" name="comprovante" id="modal_comprovante" class="mt-1 block w-full"/>
                </div>
<div class="flex justify-end gap-2 mt-4">
    <button type="button" id="cancelModal" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
        Cancelar
    </button>
    <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
        Salvar Alterações
    </button>
    <button type="button" id="registrarPagamento" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition">
        Registrar Pagamento
    </button>
</div>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const editModal = document.getElementById("editParcelaModal");
    const closeModalBtn = document.getElementById("closeModal");
    const cancelModalBtn = document.getElementById("cancelModal");
    const editForm = document.getElementById("editParcelaForm");
    const registrarPagamentoBtn = document.getElementById("registrarPagamento");

    function formatDate(date) {
        const d = new Date(date);
        const month = ('0' + (d.getMonth() + 1)).slice(-2);
        const day = ('0' + d.getDate()).slice(-2);
        return `${d.getFullYear()}-${month}-${day}`;
    }

    function abrirModal(dados){
        const hoje = formatDate(new Date());
        document.getElementById("modal_descricao").value = dados.descricao || '';
        document.getElementById("modal_valor").value = dados.valor_total || dados.valor_parcela || 0;
        document.getElementById("modal_vencimento").value = dados.data_vencimento || hoje;
        document.getElementById("modal_pagamento").value = dados.data_pagamento || hoje;
        document.getElementById("modal_comprovante").value = '';
        editForm.action = dados.action || '';
        editModal.classList.remove("hidden");
    }

    // Botões Editar
    document.querySelectorAll('button[data-despesa]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            abrirModal(JSON.parse(btn.dataset.despesa));
        });
    });

    // Double click abre modal apenas para parcelas
    document.querySelectorAll('tr[data-parcela-id]').forEach(tr => {
        tr.addEventListener('dblclick', () => {
            const btnEditar = tr.querySelector('button[data-parcela]');
            if(btnEditar) {
                const parcelaDados = JSON.parse(btnEditar.dataset.parcela);
                abrirModal(parcelaDados);
            }
        });
    });

    // Fechar modal
    closeModalBtn.addEventListener("click", () => editModal.classList.add("hidden"));
    cancelModalBtn.addEventListener("click", () => editModal.classList.add("hidden"));
    document.addEventListener('keydown', (e) => {
        if(e.key === "Escape") editModal.classList.add("hidden");
    });

    // Registrar pagamento incluindo descrição e comprovante
    registrarPagamentoBtn.addEventListener("click", () => {
        if(confirm("Deseja registrar o pagamento desta parcela como PAGO?")) {
            const parcelaId = editForm.action.split('/').pop();
            const formData = new FormData();

            formData.append('data_pagamento', document.getElementById("modal_pagamento").value);
            formData.append('descricao', document.getElementById("modal_descricao").value);

            const comprovanteFile = document.getElementById("modal_comprovante").files[0];
            if(comprovanteFile){
                formData.append('comprovante', comprovanteFile);
            }
fetch(`/despesas/${parcelaId}/registrar-pagamento`, {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: formData
})
            .then(response => response.ok ? location.reload() : alert('Erro ao registrar pagamento'))
            .catch(() => alert('Erro ao registrar pagamento'));
        }
    });

    // Expandir/ocultar parcelas
    document.querySelectorAll('.despesa-row').forEach(row => {
        row.addEventListener('click', (e) => {
            if (e.target.tagName === 'BUTTON') return;
            const despesaId = row.getAttribute('data-despesa-id');
            document.querySelectorAll('.parcela-' + despesaId).forEach(r => r.classList.toggle('hidden'));
        });
    });

    // Checkbox exibir todas parcelas
    document.getElementById('toggle-all-parcelas').addEventListener('change', function(){
        document.querySelectorAll('[class*="parcela-"]').forEach(r => {
            this.checked ? r.classList.remove('hidden') : r.classList.add('hidden');
        });
    });
});
</script>
</x-app-layout>
