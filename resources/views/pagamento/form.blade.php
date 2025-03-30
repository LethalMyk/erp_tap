<x-app-layout>
    <div class="container">
        <h2>Pagamento do Pedido #{{ $pedido->pedido_id }}</h2>

        <!-- Exibe o status e orçamento do pedido -->
        <div>
            <strong>Status:</strong> {{ $pedido->status }}<br>
            <strong>Orçamento Total:</strong> R$ {{ number_format($pedido->orcamento, 2, ',', '.') }}<br>
            <strong>Valor Pago:</strong> R$ {{ number_format($pagamentos->sum('valor'), 2, ',', '.') }}<br>
        </div>

        <!-- Exibe os pagamentos realizados -->
        <h3>Pagamentos Realizados</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Valor</th>
                    <th>Forma de Pagamento</th>
                    <th>Descrição</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagamentos as $pagamento)
                <tr>
                    <td>R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</td>
                    <td>{{ $pagamento->forma }}</td>
                    <td>{{ $pagamento->descricao }}</td>
                    <td>{{ $pagamento->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Formulário para registrar um novo pagamento -->
        <h3>Registrar Pagamento</h3>
        <form action="{{ route('pagamento.store', $pedido->pedido_id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="valor">Valor</label>
                <input type="number" name="valor" id="valor" class="form-control" value="{{ old('valor') }}" required>
            </div>
            <div class="form-group">
                <label for="forma">Forma de Pagamento</label>
                <input type="text" name="forma" id="forma" class="form-control" value="{{ old('forma') }}" required>
            </div>
            <div class="form-group">
                <label for="descricao">Descrição</label>
                <input type="text" name="descricao" id="descricao" class="form-control" value="{{ old('descricao') }}">
            </div>
            <button type="submit" class="btn btn-primary">Registrar Pagamento</button>
        </form>

        @if(session('success'))
            <div class="alert alert-success mt-3">
                {{ session('success') }}
            </div>
        @endif
    </div>
</x-app-layout>
