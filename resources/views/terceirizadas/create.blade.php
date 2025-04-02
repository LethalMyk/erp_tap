<x-app-layout>
    <h1>Novo Serviço Terceirizado</h1>

    <form action="{{ route('terceirizadas.store') }}" method="POST">
        @csrf

        <label>Selecione o Pedido</label>
        <select id="pedido_id" name="pedido_id" required>
            <option value="">-- Escolha um Pedido --</option>
            @foreach($pedidos as $pedido)
                <option value="{{ $pedido->id }}">Pedido #{{ $pedido->id }}</option>
            @endforeach
        </select>

        <label>Selecione o Item</label>
        <select id="item_id" name="item_id" required>
            <option value="">-- Escolha um Item --</option>
        </select>

<select name="tipoServico" class="form-control">
    @foreach(['Impermeabilizar', 'Higienizar', 'Pintar', 'Invernizar', 'Outros'] as $servico)
        <option value="{{ $servico }}">{{ $servico }}</option>
    @endforeach
</select>

        <label>Observações</label>
        <textarea name="obs"></textarea>

        <button type="submit">Salvar</button>
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
