<x-app-layout>
    <h1>Editar Serviço Terceirizado</h1>

    <form action="{{ route('terceirizadas.update', $terceirizada->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Selecione o Pedido</label>
        <select id="pedido_id" name="pedido_id" required>
            <option value="">-- Escolha um Pedido --</option>
            @foreach($pedidos as $pedido)
                <option value="{{ $pedido->id }}" 
                    @if($pedido->id == $terceirizada->pedido_id) selected @endif>
                    Pedido #{{ $pedido->id }}
                </option>
            @endforeach
        </select>

        <label>Selecione o Item</label>
        <select id="item_id" name="item_id" required>
            <option value="">-- Escolha um Item --</option>
            @if($pedidoSelecionado && $pedidoSelecionado->items->count() > 0)
                @foreach($pedidoSelecionado->items as $item)
                    <option value="{{ $item->id }}" 
                        @if($terceirizada->item_id == $item->id) selected @endif>
                        {{ $item->descricao }}
                    </option>
                @endforeach
            @endif
        </select>

        <label>Tipo de Serviço</label>
        <input type="text" name="tipoServico" value="{{ $terceirizada->tipoServico }}" required>

        <label>Observações</label>
        <textarea name="obs">{{ $terceirizada->obs }}</textarea>

        <button type="submit">Atualizar</button>
    </form>

    <script>
        document.getElementById('pedido_id').addEventListener('change', function() {
            var pedidoId = this.value;
            var itemSelect = document.getElementById('item_id');
            itemSelect.innerHTML = '<option value="">-- Escolha um Item --</option>';

            if (pedidoId) {
                fetch(`/get-items/${pedidoId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            var option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = item.descricao;
                            itemSelect.appendChild(option);
                        });
                    });
            }
        });
    </script>
</x-app-layout>
