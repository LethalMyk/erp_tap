 <x-app-layout>

 <form action="{{ route('items.store') }}" method="POST">
    @csrf
    <label for="pedido_id">Pedido:</label>
    <select name="pedido_id" required>
        @foreach ($pedidos as $pedido)
            <option value="{{ $pedido->id }}">Pedido #{{ $pedido->id }}</option>
        @endforeach
    </select>

    <label for="nomeItem">Nome do Item:</label>
    <input type="text" name="nomeItem" required>

    <label for="material">Material:</label>
    <input type="text" name="material" required>

    <label for="metragem">Metragem:</label>
    <input type="number" name="metragem" step="0.01" required>

    <label for="especifi">Especificações:</label>
    <textarea name="especifi"></textarea>

    <button type="submit">Salvar Item</button>
</form>

</x-app-layout>
