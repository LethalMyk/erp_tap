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

        <a href="{{ route('despesas.create') }}" 
           class="bg-blue-700 text-white font-semibold px-5 py-3 rounded-lg mb-4 inline-block shadow border border-blue-900 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition">
            Nova Despesa
        </a>

        <div class="overflow-x-auto bg-white shadow rounded" x-data="{ editModalOpen: false, editDespesa: null }">

            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
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
                        <tr>
                            <td class="px-4 py-2">{{ $despesa->id }}</td>
                            <td class="px-4 py-2">{{ $despesa->descricao }}</td>
                            <td class="px-4 py-2">R$ {{ number_format($despesa->valor, 2, ',', '.') }}</td>
                            <td class="px-4 py-2">{{ $despesa->data_vencimento->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">
                                @if($despesa->data_pagamento)
                                    {{ $despesa->data_pagamento->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
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
                            <td class="px-4 py-2">{{ $despesa->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">{{ $despesa->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-2">
                                <button 
                                    class="bg-yellow-600 text-white font-semibold px-5 py-2 rounded-lg shadow border border-yellow-800 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-1 transition"
                                    @click="
                                        editModalOpen = true;
                                        editDespesa = {{ json_encode($despesa) }};
                                    ">
                                    Editar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Modal de edição -->
            <div
                x-show="editModalOpen"
                style="display: none"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                @keydown.escape.window="editModalOpen = false"
            >
                <div class="bg-white rounded-lg p-6 w-full max-w-lg relative" @click.away="editModalOpen = false">
                    <h3 class="text-lg font-semibold mb-4">Editar Despesa #<span x-text="editDespesa?.id"></span></h3>

                    <form :action="`/despesas/${editDespesa?.id}`" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="block mb-1 font-medium">Descrição</label>
                            <input type="text" name="descricao" x-model="editDespesa.descricao" class="w-full border rounded px-2 py-1" required>
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium">Valor</label>
                            <input type="number" step="0.01" name="valor" x-model="editDespesa.valor" class="w-full border rounded px-2 py-1" required>
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium">Data Vencimento</label>
                            <input type="date" name="data_vencimento" x-model="editDespesa.data_vencimento" class="w-full border rounded px-2 py-1" required>
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium">Data Pagamento</label>
                            <input type="date" name="data_pagamento" x-model="editDespesa.data_pagamento" class="w-full border rounded px-2 py-1">
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium">Status</label>
                            <select name="status" x-model="editDespesa.status" class="w-full border rounded px-2 py-1" required>
                                <option value="PENDENTE">PENDENTE</option>
                                <option value="PAGO">PAGO</option>
                                <option value="ATRASADO">ATRASADO</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium">Categoria</label>
                            <select name="categoria" x-model="editDespesa.categoria" class="w-full border rounded px-2 py-1" required>
                                <option value="FORNECEDOR">FORNECEDOR</option>
                                <option value="AGUA">AGUA</option>
                                <option value="LUZ">LUZ</option>
                                <option value="MATERIAL">MATERIAL</option>
                                <option value="PARTICULAR">PARTICULAR</option>
                                <option value="OUTROS">OUTROS</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium">Forma Pagamento</label>
                            <select name="forma_pagamento" x-model="editDespesa.forma_pagamento" class="w-full border rounded px-2 py-1" required>
                                <option value="PIX">PIX</option>
                                <option value="DINHEIRO">DINHEIRO</option>
                                <option value="BOLETO">BOLETO</option>
                                <option value="CARTAO">CARTAO</option>
                                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                <option value="OUTROS">OUTROS</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium">Chave Pagamento</label>
                            <input type="text" name="chave_pagamento" x-model="editDespesa.chave_pagamento" class="w-full border rounded px-2 py-1">
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium">Comprovante (substituir)</label>
                            <input type="file" name="comprovante" accept=".jpg,.jpeg,.png,.pdf" class="w-full">
                        </div>

                        <div class="mb-3">
                            <label class="block mb-1 font-medium">Observação</label>
                            <textarea name="observacao" x-model="editDespesa.observacao" class="w-full border rounded px-2 py-1"></textarea>
                        </div>

                        <div class="flex justify-end gap-4 mt-6">
                            <button type="button" 
                                @click="editModalOpen = false" 
                                class="px-6 py-3 bg-gray-900 text-white font-semibold rounded-lg shadow border border-gray-800 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-700 focus:ring-offset-1 transition">
                                Cancelar
                            </button>
                            <button type="submit" 
                                class="px-6 py-3 bg-green-700 text-white font-semibold rounded-lg shadow border border-green-800 hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-1 transition">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        <div class="mt-4">
            {{ $despesas->links() }}
        </div>
    </div>
</x-app-layout>
