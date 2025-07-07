<h5>Agendamentos (Retirada, Entrega, Assistência e Outros)</h5>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Data</th>
            <th>Tipo</th>
            <th>Cliente</th>
            <th>Horário</th>
            <th>Endereço</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($agendamentos as $ag)
            <tr data-agendamento='@json($ag)' style="cursor: pointer;">
                <td>{{ \Carbon\Carbon::parse($ag->data)->format('d/m/Y') }}</td>
                <td class="text-capitalize">{{ $ag->tipo }}</td>
                <td>{{ $ag->nome_cliente }}</td>
                <td>{{ $ag->horario }}</td>
                <td>{{ $ag->endereco }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">Nenhum agendamento encontrado.</td>
            </tr>
        @endforelse
    </tbody>
</table>
