<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Orçamentos</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('orcamentos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Novo Orçamento</a>

        <table class="w-full mt-6 border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">Cliente</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Valor Total</th>
                    <th class="border px-4 py-2">Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orcamentos as $orc)
                    <tr>
                        <td class="border px-4 py-2">{{ $orc->cliente_nome }}</td>
                        <td class="border px-4 py-2">{{ $orc->status }}</td>
                        <td class="border px-4 py-2">R$ {{ number_format($orc->valor_total, 2, ',', '.') }}</td>
                        <td class="border px-4 py-2">{{ $orc->created_at->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
