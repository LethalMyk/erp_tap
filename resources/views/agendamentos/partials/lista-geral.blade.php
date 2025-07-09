<h5 class="abrir-novo-agendamento" style="cursor: pointer;">📋 Entregas e Retiradas</h5>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Data</th>
            <th>Cliente</th>
            <th>Endereço</th>
            <th>Telefone</th> {{-- Nova coluna --}}
            <th>Tipo</th>
            <th>Horário</th>
            <th>Observação</th> {{-- Nova coluna adicionada --}}
        </tr>
    </thead>
    <tbody>
        @forelse ($agendamentos as $ag)
            <tr data-agendamento='@json($ag)' style="cursor: pointer;">
                <td>{{ \Carbon\Carbon::parse($ag->data)->format('d/m/Y') }}</td>
                <td>{{ $ag->nome_cliente }}</td>
                <td>{{ $ag->endereco }}</td>
                <td>{{ $ag->telefone ?? '-' }}</td>
                <td class="text-capitalize">{{ $ag->tipo }}</td>
                <td>{{ $ag->horario }}</td>
                <td>{{ $ag->observacao ?? '-' }}</td> {{-- Mostra observação ou '-' --}}
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Nenhum agendamento encontrado.</td>
            </tr>
        @endforelse
    </tbody>
</table>
