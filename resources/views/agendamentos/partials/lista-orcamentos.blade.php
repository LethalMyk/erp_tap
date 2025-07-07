<h5>Orçamentos</h5>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Data</th>
            <th>Cliente</th>
            <th>Horário</th>
            <th>Endereço</th>
            <th>Telefone</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orcamentos as $orc)
            <tr>
                <td>{{ \Carbon\Carbon::parse($orc->data)->format('d/m/Y') }}</td>
                <td>{{ $orc->nome_cliente }}</td>
                <td>{{ $orc->horario }}</td>
                <td>{{ $orc->endereco }}</td>
                <td>{{ $orc->telefone ?? '-' }}</td>
                <td>{{ $orc->observacao ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Nenhum orçamento encontrado.</td>
            </tr>
        @endforelse
    </tbody>
</table>
