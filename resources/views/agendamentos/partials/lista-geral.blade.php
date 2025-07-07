<h5>Agendamentos (Retirada, Entrega, Assistência e Outros)</h5>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Data</th>
            <th>Cliente</th>
            <th>Endereço</th>
            <th>Telefone</th> {{-- Nova coluna --}}
            <th>Tipo</th>
            <th>Horário</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($agendamentos as $ag)
            <tr data-agendamento='@json($ag)' style="cursor: pointer;">
                <td>{{ \Carbon\Carbon::parse($ag->data)->format('d/m/Y') }}</td>
                <td>{{ $ag->nome_cliente }}</td>
                <td>{{ $ag->endereco }}</td>
                <td>{{ $ag->telefone ?? '-' }}</td> {{-- Aqui mostra telefone --}}
                <td class="text-capitalize">{{ $ag->tipo }}</td>
                <td>{{ $ag->horario }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Nenhum agendamento encontrado.</td>
            </tr>
        @endforelse
    </tbody>
</table>
