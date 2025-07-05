@if($agendamentos->isEmpty())
    <p class="text-muted">Nenhum agendamento.</p>
@else
    <ul class="list-group mb-4">
        @foreach($agendamentos as $ag)
       <li class="list-group-item d-flex justify-content-between align-items-start">
    <div>
        <strong>{{ \Carbon\Carbon::parse($ag->data)->format('d/m/Y') }} {{ substr($ag->horario, 0, 5) }}</strong> |
        <span class="text-uppercase">{{ $ag->tipo }}</span> |
        <span>{{ $ag->nome_cliente }}</span> |
        <span class="badge bg-secondary">{{ $ag->status }}</span>
        <br>
        <small class="text-muted">{{ $ag->endereco }}</small>
        @if($ag->itens)
            <br><em>🛋️ Itens: {{ $ag->itens }}</em>
        @endif
        @if($ag->observacao)
            <br><em>📝 Obs: {{ $ag->observacao }}</em>
        @endif
    </div>
    <div>
        <a href="{{ route('agendamentos.edit', $ag->id) }}" class="btn btn-sm btn-warning">Editar</a>
        <form action="{{ route('agendamentos.destroy', $ag->id) }}" method="POST" class="d-inline">
            @csrf @method('DELETE')
            <button onclick="return confirm('Tem certeza que deseja cancelar?')" class="btn btn-sm btn-danger">Cancelar</button>
        </form>
    </div>
</li>

        @endforeach
    </ul>
@endif
