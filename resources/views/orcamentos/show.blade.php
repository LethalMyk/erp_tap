<x-app-layout>
  <x-slot name="header"><h2>Orçamento #{{ $orcamento->id }}</h2></x-slot>

  <div class="max-w-4xl mx-auto bg-white p-6 rounded">
    <h3 class="text-lg font-bold">{{ $orcamento->cliente_nome }}</h3>
    <p>Telefone: {{ $orcamento->telefone }}</p>
    <p>Email: {{ $orcamento->email }}</p>
    <p class="mt-4">Observações: {{ $orcamento->observacoes }}</p>

    <table class="w-full mt-4 border-collapse">
      <thead class="bg-gray-100"><tr><th>Item</th><th>Tecido</th><th>Quantidade</th><th>Unit</th><th>Subtotal</th></tr></thead>
      <tbody>
        @foreach($orcamento->itens as $item)
          <tr>
            <td class="border px-2 py-1">{{ $item->item }}<br>
              <small>{{ implode(', ', array_keys($item->espumas ?? [])) }} {{ implode(', ', array_keys($item->enchimentos ?? [])) }}</small>
            </td>
            <td class="border px-2 py-1">{{ $item->tecido }}</td>
            <td class="border px-2 py-1">{{ $item->quantidade }}</td>
            <td class="border px-2 py-1">R$ {{ number_format($item->valor_total,2,',','.') }}</td>
            <td class="border px-2 py-1">R$ {{ number_format($item->subtotal,2,',','.') }}</td>
          </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr><td colspan="4" class="text-right font-bold">Total</td><td class="font-bold">R$ {{ number_format($orcamento->valor_total,2,',','.') }}</td></tr>
      </tfoot>
    </table>

    <div class="mt-4 flex gap-2">
      <a href="{{ route('orcamentos.index') }}" class="px-3 py-2 bg-gray-500 text-white rounded">Voltar</a>
      <a href="{{ route('orcamentos.pdf', $orcamento->id) }}" class="px-3 py-2 bg-blue-600 text-white rounded">Gerar PDF</a>
      <form action="{{ route('orcamentos.converter', $orcamento->id) }}" method="POST" onsubmit="return confirm('Converter para pedido?')">
        @csrf
        <button class="px-3 py-2 bg-green-600 text-white rounded">Aprovar & Converter</button>
      </form>
    </div>
  </div>
</x-app-layout>
