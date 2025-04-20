<div id="simplificada" class="view" style="display: none;">
    <h2>Visualização Simplificada</h2>

    <h3>Cliente</h3>
    <p><strong>Nome:</strong> {{ $pedido->cliente->nome }}</p>
    <p><strong>Telefone:</strong> {{ $pedido->cliente->telefone }}</p>

    <h3>Pedido</h3>
    <p><strong>Valor Total:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</p>

  <a href="{{ route('pedidos.imprimir.viasimplificada', $pedido->id) }}" target="_blank" class="btn btn-primary" style="margin-top: 20px;">
    🖨️ Imprimir Via Simplificada
</a>

</div>
