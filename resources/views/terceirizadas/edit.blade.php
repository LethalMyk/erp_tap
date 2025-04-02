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
        </select>

        <label>Tipo de Serviço</label>
        <input type="text" name="tipoServico" value="{{ $terceirizada->tipoServico }}" required>

        <label>Observações</label>
        <textarea name="obs">{{ $terceirizada->obs }}</textarea>

        <button type="submit">Atualizar</button>
    </form>

    <script>
        function carregarItens(pedidoId, itemSelecionado = null) {
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
                            if (itemSelecionado && item.id == itemSelecionado) {
                                option.selected = true;
                            }
                            itemSelect.appendChild(option);
                        });
                    });
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            var pedidoSelect = document.getElementById('pedido_id');
            var pedidoSelecionado = pedidoSelect.value;
            var itemSelecionado = "{{ $terceirizada->item_id }}"; // Recupera o item já salvo

            if (pedidoSelecionado) {
                carregarItens(pedidoSelecionado, itemSelecionado);
            }

            pedidoSelect.addEventListener('change', function() {
                carregarItens(this.value);
            });
        });
    </script>
</x-app-layout>
