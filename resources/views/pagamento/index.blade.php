<x-app-layout>

<div class="container">
    <h1>Lista de Pagamentos</h1>

    <div style="text-align: right; margin-bottom: 15px;">
        <a href="{{ route('pagamento.create') }}"
           style="background-color: #28a745; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">
            + Novo Pagamento
        </a>
    </div>

    @if(session('success'))
        <div style="color: green; margin-bottom: 10px;">
            {{ session('success') }}
        </div>
    @endif


<table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>Pedido ID</th>
            <th>Cliente</th>
            <th>Endereço</th>
            <th>Valor Total</th>
            <th>Valor Pagamento</th>
            <th>Status do Pagamento</th>
            <th>Status do Pedido</th> <!-- NOVA COLUNA -->
            <th>Observação</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @forelse($pagamentos as $pagamento)
            <tr>
                <td>{{ $pagamento->pedido->id ?? 'N/A' }}</td>
                <td>{{ $pagamento->pedido->cliente->nome ?? 'N/A' }}</td>
                <td>{{ $pagamento->pedido->cliente->endereco ?? 'N/A' }}</td>
                <td>R$ {{ number_format($pagamento->pedido->valor ?? 0, 2, ',', '.') }}</td>
                <td>R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</td>
                <td>{{ $pagamento->status }}</td>
                <td>{{ $pagamento->pedido->status ?? 'N/A' }}</td> <!-- NOVA LINHA -->
                <td>{{ $pagamento->obs }}</td>
                <td>
                    <td>
    <a href="{{ route('pagamento.edit', $pagamento->id) }}">Editar</a>

   @if($pagamento->status === 'EM ABERTO')
    |
    <button type="button" onclick="toggleRegistrar({{ $pagamento->id }})">Registrar</button>

    <div id="registrar-form-{{ $pagamento->id }}" style="display: none; margin-top: 5px;">
        <form action="{{ route('pagamento.registrar', $pagamento->id) }}" method="POST">
            @csrf
            <textarea name="obs" rows="2" placeholder="Observação (opcional)" style="width: 100%;"></textarea>

            <label for="data_registro_{{ $pagamento->id }}">Data do Registro:</label>
            <input type="date" name="data_registro" id="data_registro_{{ $pagamento->id }}" value="{{ date('Y-m-d') }}" style="width: 100%; margin-bottom: 5px;" />

            <button type="submit" style="margin-top: 5px;">Confirmar Registro</button>
        </form>
    </div>
@endif


    |
    <form action="{{ route('pagamento.destroy', $pagamento->id) }}" method="POST" style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Tem certeza que deseja excluir?');">Excluir</button>
    </form>
</td>

            </tr>
        @empty
            <tr><td colspan="9" style="text-align:center;">Nenhum pagamento encontrado.</td></tr>
        @endforelse
    </tbody>
</table>
</div>
<script>
    function toggleRegistrar(id) {
        const form = document.getElementById('registrar-form-' + id);
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
</script>

</x-app-layout>
