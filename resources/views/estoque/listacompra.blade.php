<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lista de Compras</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Tabela de Itens Ativos --}}
        <div class="mb-6 overflow-x-auto shadow rounded-lg">
            <h3 class="px-6 py-3 font-semibold text-gray-700">Itens Ativos</h3>
            <table class="min-w-full bg-white divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pedido ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metragem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fornecedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estoque</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Situação</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($compras->where('arquivado', false) as $compra)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $compra->pedido->id ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $compra->material }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('estoque.listacompra.atualizarSituacao', $compra->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="metragem" value="{{ $compra->metragem }}" min="0"
                                        class="border rounded px-2 py-1 w-20" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('estoque.listacompra.atualizarSituacao', $compra->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="fornecedor" value="{{ $compra->fornecedor }}"
                                        class="border rounded px-2 py-1 w-32" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $estoqueProduto = \App\Models\Estoque::whereHas('produto', function($q) use ($compra) {
                                        $q->where('nome', $compra->material);
                                    })->first();
                                    $quantidadeDisponivel = $estoqueProduto ? $estoqueProduto->quantidade_disponivel : 0;
                                @endphp
                                {{ $quantidadeDisponivel }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('estoque.listacompra.atualizarSituacao', $compra->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="situacao" class="border rounded px-2 py-1" onchange="this.form.submit()">
                                        @foreach(['comprado','pendente','solicitado','em falta','fora de linha'] as $situacao)
                                            <option value="{{ $situacao }}" @if($compra->situacao == $situacao) selected @endif>
                                                {{ ucfirst($situacao) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">Detalhes</button>
                                <form method="POST" action="{{ route('estoque.listacompra.arquivar', $compra->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600 text-sm">
                                        Arquivar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nenhum item pendente.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tabela de Itens Arquivados --}}
        <div class="overflow-x-auto shadow rounded-lg">
            <h3 class="px-6 py-3 font-semibold text-gray-700">Itens Arquivados</h3>
            <table class="min-w-full bg-white divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pedido ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metragem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fornecedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estoque</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Situação</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-50 divide-y divide-gray-200">
                    @forelse($compras->where('arquivado', true) as $compra)
                        <tr class="hover:bg-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $compra->pedido->id ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $compra->material }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $compra->metragem }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $compra->fornecedor }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $estoqueProduto = \App\Models\Estoque::whereHas('produto', function($q) use ($compra) {
                                        $q->where('nome', $compra->material);
                                    })->first();
                                    $quantidadeDisponivel = $estoqueProduto ? $estoqueProduto->quantidade_disponivel : 0;
                                @endphp
                                {{ $quantidadeDisponivel }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($compra->situacao) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('estoque.listacompra.desarquivar', $compra->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-sm">
                                        Desarquivar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nenhum item arquivado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
