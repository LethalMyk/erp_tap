<h5>Orçamentos</h5>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Horário</th>
            <th>Endereço</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($orcamentos as $orc)
            <tr>
                <td>{{ $orc->nome_cliente }}</td>
                <td>{{ $orc->horario }}</td>
                <td>{{ $orc->endereco }}</td>
                <td>{{ $orc->observacao ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Nenhum orçamento encontrado.</td>
            </tr>
        @endforelse
    </tbody>
</table>
