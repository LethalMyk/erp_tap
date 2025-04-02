<x-app-layout>
    <h1>Editar Serviço Terceirizado</h1>

    <form action="{{ route('terceirizadas.update', $terceirizada->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Selecione o Pedido</label>
        <select id="pedido_id" name="pedido_id" class="form-control" required>
            <option value="">-- Escolha um Pedido --</option>
            @foreach($pedidos as $pedido)
                <option value="{{ $pedido->id }}" 
                    {{ $pedido->id == $terceirizada->pedido_id ? 'selected' : '' }}>
                    Pedido #{{ $pedido->id }}
                </option>
            @endforeach
        </select>

        <label>Selecione o Item</label>
        <select id="item_id" name="item_id" class="form-control" required>
            <option value="">-- Escolha um Item --</option>
        </select>

        <label>Tipo de Serviço</label>
        <select name="tipoServico" class="form-control">
            @foreach(['Impermeabilizar', 'Higienizar', 'Pintar', 'Invernizar', 'Outros'] as $servico)
                <option value="{{ $servico }}" 
                    {{ $terceirizada->tipoServico == $servico ? 'selected' : '' }}>
                    {{ $servico }}
                </option>
            @endforeach
        </select>

        <label>Observações</label>
        <textarea name="obs" class="form-control">{{ $terceirizada->obs }}</textarea>

        <label>Andamento</label>
        <select name="andamento" class="form-control">
            @foreach(['em espera', 'executado', 'pronto'] as $status)
                <option value="{{ $status }}" {{ $terceirizada->andamento == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>

        <label>Valor</label>
        <input type="number" step="0.01" name="valor" class="form-control" value="{{ $terceirizada->valor }}">

        <label>Status do Pagamento</label>
        <select name="statusPg" class="form-control">
            @foreach(['Pendente', 'Pago', 'Parcial'] as $status)
                <option value="{{ $status }}" {{ $terceirizada->statusPg == $status ? 'selected' : '' }}>
                    {{ $status }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary mt-3">Atualizar</button>
    </form>

    <script>
        function carregarItens(pedidoId, itemSelecionado = null) {
            let itemSelect = document.getElementById('item_id');
            itemSelect.innerHTML = '<option value="">-- Escolha um Item --</option>';

            if (pedidoId) {
                fetch(`/get-items/${pedidoId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(item => {
                            let option = document.createElement('option');
                            option.value = item.id;
                            option.textContent = item.descricao;
                            if (itemSelecionado && item.id == itemSelecionado) {
                                option.selected = true;
                            }
                            itemSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Erro ao carregar itens:', error));
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            let pedidoSelect = document.getElementById('pedido_id');
            let pedidoSelecionado = pedidoSelect.value;
            let itemSelecionado = "{{ $terceirizada->item_id }}";

            if (pedidoSelecionado) {
                carregarItens(pedidoSelecionado, itemSelecionado);
            }

            pedidoSelect.addEventListener('change', function() {
                carregarItens(this.value);
            });
        });
    </script>
</x-app-layout>
