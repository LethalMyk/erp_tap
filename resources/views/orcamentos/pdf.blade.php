<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Orçamento #{{ $orcamento->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; }
        .text-right { text-align: right; }
        .title { text-align: center; font-size: 18px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="title">Orçamento #{{ $orcamento->id }}</div>

    <div>
        <strong>Cliente:</strong> {{ $orcamento->cliente_nome }}<br>
        <strong>Telefone:</strong> {{ $orcamento->telefone ?? '-' }}<br>
        <strong>Email:</strong> {{ $orcamento->email ?? '-' }}<br>
        <strong>Status:</strong> {{ $orcamento->status }}<br>
        <strong>Observações:</strong> {{ $orcamento->observacoes ?? '-' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Tecido</th>
                <th>Espumas / Enchimentos / Adicionais</th>
                <th>Qtd</th>
                <th>Unitário</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orcamento->itens as $item)
                <tr>
                    <td>{{ $item->item }}</td>
                    <td>{{ $item->tecido ?? '-' }}</td>
                    <td>
                        @php
                            $espumas = json_decode($item->espumas, true) ?? [];
                            $enchimentos = json_decode($item->enchimentos, true) ?? [];
                            $adicionais = json_decode($item->adicionais, true) ?? [];
                        @endphp
                        {{ implode(', ', $espumas) }} {{ implode(', ', $enchimentos) }} {{ implode(', ', $adicionais) }}
                    </td>
                    <td class="text-right">{{ $item->quantidade }}</td>
                    <td class="text-right">R$ {{ number_format($item->valor_total,2,',','.') }}</td>
                    <td class="text-right">R$ {{ number_format($item->subtotal,2,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">Total</th>
                <th class="text-right">R$ {{ number_format($orcamento->valor_total,2,',','.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 20px;">
        <strong>Observação:</strong> Este orçamento é válido por 7 dias.
    </div>

</body>
</html>
