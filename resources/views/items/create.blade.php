 <x-app-layout>

<div class="container">
    <h1>Adicionar Item ao Pedido</h1>

    <form action="{{ route('pedidos.itens.store', $pedido->id) }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nome_item">Nome do Item</label>
            <input type="text" name="nome_item" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="quant_item">Quantidade</label>
            <input type="number" name="quant_item" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="tecido_item">Tecido</label>
            <input type="text" name="tecido_item" class="form-control">
        </div>
        <div class="form-group">
            <label for="metragem_item">Metragem</label>
            <input type="number" name="metragem_item" class="form-control">
        </div>
        <div class="form-group">
            <label for="desc_item">Descrição</label>
            <textarea name="desc_item" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="obs_item">Observações</label>
            <textarea name="obs_item" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Adicionar Item</button>
    </form>
</div>
</x-app-layout>
