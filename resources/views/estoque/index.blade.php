<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Estoque Disponível</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 px-4">
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
                @forelse($produtos as $p)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $p->nome }}</td>
                    <td class="px-4 py-2">{{ $p->categoria }}</td>
                    <td class="px-4 py-2">{{ $p->quantidade_disponivel }}</td>
                    <td class="px-4 py-2">{{ $p->unidade_medida }}</td>
                    <td class="px-4 py-2">R$ {{ number_format($p->valor_unitario, 2, ',', '.') }}</td>
                    <td class="px-4 py-2">R$ {{ number_format($p->quantidade_disponivel * $p->valor_unitario, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center">Nenhum produto disponível no estoque.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
