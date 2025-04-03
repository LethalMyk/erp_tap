<x-app-layout>
    <h1>Criar Pedido</h1>

    <form action="{{ route('pedidos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="cliente_id">Cliente</label>
            <select name="cliente_id" id="cliente_id">
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="qntItens">Quantidade de Itens</label>
            <input type="number" name="qntItens" id="qntItens" required>
        </div>
        <div>
            <label for="data">Data do Pedido</label>
            <input type="date" name="data" id="data" required>
        </div>
        <div>
            <label for="valor">Valor</label>
            <input type="text" name="valor" id="valor" required>
        </div>
        <div>
            <label for="obs">Observações</label>
            <textarea name="obs" id="obs"></textarea>
        </div>
        <div>
            <label for="prazo">Prazo</label>
            <input type="date" name="prazo" id="prazo" required>
        </div>
        <div>
            <label for="imagens">Anexar Imagens:</label>
            <input type="file" name="imagens[]" multiple>
        </div>
        <button type="submit">Criar Pedido</button>
    </form>
</x-app-layout>
