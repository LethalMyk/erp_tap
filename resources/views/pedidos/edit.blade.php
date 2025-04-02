<x-app-layout>
    <h1>Editar Pedido</h1>

    <form action="{{ route('pedidos.update', $pedido->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <label for="cliente_id">Cliente</label>
            <select name="cliente_id" id="cliente_id">
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" @if($cliente->id == $pedido->cliente_id) selected @endif>
                        {{ $cliente->nome }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="qntItens">Quantidade de Itens</label>
            <input type="number" name="qntItens" id="qntItens" value="{{ $pedido->qntItens }}" required>
        </div>
        <div>
            <label for="data">Data do Pedido</label>
            <input type="date" name="data" id="data" value="{{ $pedido->data }}" required>
        </div>
        <div>
            <label for="valor">Valor</label>
            <input type="text" name="valor" id="valor" value="{{ $pedido->valor }}" required>
        </div>
        <div>
            <label for="status">Status</label>
            <input type="text" name="status" id="status" value="{{ $pedido->status }}" required>
        </div>
        <div>
            <label for="obs">Observações</label>
            <textarea name="obs" id="obs">{{ $pedido->obs }}</textarea>
        </div>
        <div>
            <label for="prazo">Prazo</label>
            <input type="date" name="prazo" id="prazo" value="{{ $pedido->prazo }}" required>
        </div>
        <div>
            <label for="imagens">Anexar Imagens</label>
            <input type="file" name="imagens[]" multiple>
        </div>

        <button type="submit">Atualizar Pedido</button>
    </form>

 <h3>Imagens do Pedido:</h3>
@if ($pedido->imagens->count())
    @foreach ($pedido->imagens as $imagem)
        <div style="display: flex; align-items: center; gap: 10px;">
            <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do Pedido" width="200">
            
            <form action="{{ route('pedidos.imagens.destroy', [$pedido->id, $imagem->id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Tem certeza que deseja excluir esta imagem?')">🗑️ Excluir</button>
            </form>
        </div>
    @endforeach
@else
    <p>Sem imagens anexadas.</p>
@endif
</x-app-layout>
