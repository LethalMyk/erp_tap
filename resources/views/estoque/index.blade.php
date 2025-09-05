<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Estoque Disponível</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 px-4" x-data="estoqueModal()">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        <table class="min-w-full border rounded overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left">Produto</th>
                    <th class="px-4 py-2 text-left">Categoria</th>
                    <th class="px-4 py-2 text-left">Quantidade</th>
                    <th class="px-4 py-2 text-left">Unidade</th>
                    <th class="px-4 py-2 text-left">Valor Unitário</th>
                    <th class="px-4 py-2 text-left">Valor Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produtos as $estoque)
                <tr class="border-t cursor-pointer"
                    @dblclick="abrirModal({{ $estoque->id }}, '{{ $estoque->produto->nome }}', {{ $estoque->quantidade_disponivel ?? 0 }})">
                    <td class="px-4 py-2">{{ $estoque->produto->nome }}</td>
                    <td class="px-4 py-2">{{ $estoque->produto->categoria }}</td>
                    <td class="px-4 py-2">{{ $estoque->quantidade_disponivel ?? 0 }}</td>
                    <td class="px-4 py-2">{{ $estoque->produto->unidade_medida }}</td>
                    <td class="px-4 py-2">R$ {{ number_format($estoque->produto->valor_unitario, 2, ',', '.') }}</td>
                    <td class="px-4 py-2">R$ {{ number_format(($estoque->quantidade_disponivel ?? 0) * $estoque->produto->valor_unitario, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center">Nenhum produto disponível no estoque.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Modal --}}
        <div x-show="modalOpen" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded shadow-lg w-96 p-6" @click.away="modalOpen = false">
                <h3 class="text-lg font-semibold mb-4">Editar Estoque: <span x-text="nome"></span></h3>

                <form method="POST" :action="`/estoque/${estoqueId}/quantidade`">
                    @csrf
                    @method('PUT')

                    <label class="block mb-2">Quantidade Disponível:</label>
                    <div class="flex items-center space-x-2 mb-4">
                        <button type="button" @click="novaQuantidade = Math.max(0, novaQuantidade - 1)" class="px-3 py-1 bg-gray-200 rounded">-</button>
                        <input type="number" name="quantidade_disponivel" x-model.number="novaQuantidade" min="0" class="w-full border px-3 py-2 rounded text-center">
                        <button type="button" @click="novaQuantidade++" class="px-3 py-1 bg-gray-200 rounded">+</button>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="modalOpen = false">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function estoqueModal() {
            return {
                modalOpen: false,
                estoqueId: null,
                nome: '',
                novaQuantidade: 0,
                abrirModal(id, nome, quantidade) {
                    this.estoqueId = id;
                    this.nome = nome;
                    this.novaQuantidade = Number(quantidade) || 0;
                    this.modalOpen = true;
                }
            }
        }
    </script>
</x-app-layout>
