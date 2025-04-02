 <x-app-layout>

<div class="container">
    <h1>Editar Item</h1>

    <form action="{{ route('pedidos.itens.update', [$pedido->id, $item->id]) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nome_item">Nome do Item</label>
            <input type="text" name="nome_item" class="form-control" value="{{ $item->nome_item }}" required>
        </div>
        <div class="form-group">
            <label for="quant_item">Quantidade</label>
            <input type="number" name="quant_item" class="form-control" value="{{ $item->quant_item }}" required>
        </div>
        <div class="form-group">
            <label for="tecido_item">Tecido</label>
            <input type="text" name="tecido_item" class="form-control" value="{{ $item->tecido_item }}">
        </div>
        <div class="form-group">
            <label for="metragem_item">Metragem</label>
            <input type="number" name="metragem_item" class="form-control" value="{{ $item->metragem_item }}">
        </div>
        <div class="form-group">
            <label for="desc_item">Descrição</label>
            <textarea name="desc_item" class="form-control">{{ $item->desc_item }}</textarea>
        </div>
        <div class="form-group">
            <label for="obs_item">Observações</label>
            <textarea name="obs_item" class="form-control">{{ $item->obs_item }}</textarea>
        </div>

        <button type="submit" class="btn btn-warning">Atualizar Item</button>
    </form>
</div>
</x-app-layout>
