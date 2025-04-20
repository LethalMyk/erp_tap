<div id="retirada" class="view" style="display: none;">
    <h2>Via de Retirada</h2>

    <p><strong>Data de Retirada:</strong> {{ $pedido->data_retirada }}</p>
    <p><strong>Nome do Cliente:</strong> {{ $pedido->cliente->nome }}</p>

    <a href="{{ route('pedidos.imprimir.viaretirada', $pedido->id) }}" target="_blank" class="btn btn-primary" style="margin-top: 20px;">
    🖨️ Imprimir Via de Retirada
</a>

</div>
