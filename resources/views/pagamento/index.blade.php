<x-app-layout>
    <!-- ... seu formulário e tabela acima ficam iguais ... -->
     @php
    // Pega os anos distintos dos pedidos (ou pode passar isso do Controller)
    $anos = \App\Models\Pedido::selectRaw('YEAR(created_at) as ano')->distinct()->orderBy('ano', 'desc')->pluck('ano')->toArray();

    // Meses fixos
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
    ];

    // Pega os filtros já selecionados da request para manter o estado
    $anosSelecionados = request('ano', []);
    if (!is_array($anosSelecionados)) $anosSelecionados = [$anosSelecionados];

    $mesesSelecionados = request('mes', []);
    if (!is_array($mesesSelecionados)) $mesesSelecionados = [$mesesSelecionados];
@endphp

  <h2>Filtros</h2>
    <form method="GET" action="{{ route('pagamento.index') }}" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
        <div>
            <label for="id" style="font-weight: 600;">ID:</label><br>
            <input type="number" name="id" id="id" value="{{ request('id') }}" style="padding: 5px; width: 80px;">
        </div>

        <div>
            <label for="nome" style="font-weight: 600;">Nome:</label><br>
            <input type="text" name="nome" id="nome" value="{{ request('nome') }}" style="padding: 5px;">
        </div>

        <div>
            <label for="endereco" style="font-weight: 600;">Endereço:</label><br>
            <input type="text" name="endereco" id="endereco" value="{{ request('endereco') }}" style="padding: 5px;">
        </div>

        <div>
            <label for="telefone" style="font-weight: 600;">Telefone:</label><br>
            <input type="text" name="telefone" id="telefone" value="{{ request('telefone') }}" style="padding: 5px;">
        </div>
                <div>
            <label style="font-weight: 600;">Status:</label><br>
            <select name="status" style="padding: 5px;">
                <option value="">Todos</option>
                <option value="RESTA" {{ request('status') == 'RESTA' ? 'selected' : '' }}>RESTA</option>
                <option value="PAGO" {{ request('status') == 'PAGO' ? 'selected' : '' }}>PAGO</option>
            </select>
        </div>

<div style="margin-top: 10px;">
    <label style="font-weight: 600; cursor: pointer;" onclick="togglePeriodo()" id="togglePeriodoLabel">
        Período <span id="seta" style="display: inline-block; transition: transform 0.3s;">&#x25B6;</span>
    </label>

    <div id="periodoContainer" style="display: none; max-height: 220px; overflow-y: auto; border: 1px solid #ccc; padding: 8px; border-radius: 4px; margin-top: 5px;">
        <strong>Ano:</strong><br>
        @foreach($anos as $ano)
            <label style="font-weight: normal; margin-right: 10px;">
                <input type="checkbox" name="ano[]" value="{{ $ano }}" {{ in_array($ano, $anosSelecionados) ? 'checked' : '' }}>
                {{ $ano }}
            </label>
        @endforeach

        <br><br>
        <strong>Mês:</strong><br>
        @foreach($meses as $num => $nome)
            <label style="font-weight: normal; margin-right: 10px;">
                <input type="checkbox" name="mes[]" value="{{ $num }}" {{ in_array($num, $mesesSelecionados) ? 'checked' : '' }}>
                {{ $nome }}
            </label>
        @endforeach
    </div>
</div>


@php
    $formasSelecionadas = request('forma', []);
    if (!is_array($formasSelecionadas)) {
        $formasSelecionadas = [$formasSelecionadas];
    }
    $formas = ['PIX', 'DEBITO', 'DINHEIRO', 'CREDITO À VISTA', 'CREDITO PARCELADO', 'NA ENTREGA', 'A PRAZO', 'BOLETO', 'CHEQUE', 'OUTROS'];
@endphp

<div style="margin-top: 10px;">
    <label style="font-weight: 600; cursor: pointer;" onclick="toggleForma()" id="toggleFormaLabel">
        Formas de Pagamento <span id="seta-forma" style="display: inline-block; transition: transform 0.3s;">&#x25B6;</span>
    </label>

    <div id="formaContainer" style="display: none; max-height: 150px; overflow-y: auto; border: 1px solid #ccc; padding: 8px; border-radius: 4px; margin-top: 5px;">
        @foreach($formas as $forma)
            <label style="font-weight: normal; margin-right: 10px;">
                <input type="checkbox" name="forma[]" value="{{ $forma }}" {{ in_array($forma, $formasSelecionadas) ? 'checked' : '' }}>
                {{ $forma }}
            </label>
        @endforeach
    </div>
</div>
        <div>
            <button type="submit" style="padding: 6px 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Filtrar</button>
        </div>
        <div>
            <a href="{{ route('pagamento.index') }}" style="padding: 6px 12px; background-color: #6c757d; color: white; border-radius: 4px; text-decoration: none;">Limpar</a>
        </div>
    </form>

    <table style="width: 100%; border-collapse: collapse;" id="tabela-pedidos">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Endereço</th>
                 <th>Data do Pedido</th> 
                <th>Valor do Pedido</th>
                <th>Total Pago</th>
                <th>Valor Restante</th>
                <th>Status do Pedido</th>
                <th>Observação</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pedidos as $pedidoData)
                @php
                    $pedido = $pedidoData['pedido'];
                    $pagamentos = $pedidoData['pagamentos'];
                    $totalPago = $pedidoData['total_pago'];
                    $valorResta = $pedidoData['valor_resta'];
                @endphp

                <tr onclick="togglePagamentos({{ $pedido->id }})" style="cursor: pointer;">
                    <td>{{ $pedido->id }}</td>
                    <td>{{ $pedido->cliente->nome ?? 'N/A' }}</td>
                    <td>{{ $pedido->cliente->endereco ?? 'N/A' }}</td>
   <td>{{ \Carbon\Carbon::parse($pedido->created_at)->format('d/m/Y') }}</td> <!-- NOVO -->                    <td>R$ {{ number_format($pedido->valor ?? 0, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($totalPago, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($valorResta, 2, ',', '.') }}</td>
                    <td>{{ $pedido->status ?? 'N/A' }}</td>
                    <td>{{ $pedido->obs ?? '-' }}</td>
                </tr>

                <tr id="pagamentos-{{ $pedido->id }}" style="display: none; background-color: #f9f9f9;">
                    <td colspan="8">
                        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                            <thead style="background-color: #dee2e6;">
                                <tr>
                                    <th>Data</th>
                                    <th>Valor</th>
                                    <th>Forma</th>
                                    <th>Status</th>
                                    <th>Observação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pagamentos as $p)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($p->data)->format('d/m/Y') }}</td>
                                        <td>R$ {{ number_format($p->valor, 2, ',', '.') }}</td>
                                        <td>{{ $p->forma }}</td>
                                        <td>
                                            <span style="padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; color: white;
                                                background-color: {{ $p->status === 'PAGAMENTO REGISTRADO' ? '#28a745' : '#ffc107' }};">
                                                {{ $p->status }}
                                            </span>
                                        </td>
                                        <td>{{ $p->obs ?? '-' }}</td>
                                        <td>
                                            @if($p->status === 'EM ABERTO')
                                                <button onclick="toggleRegistrar({{ $p->id }})"
                                                    style="padding: 4px 10px; font-size: 0.85rem; background: #ffc107; border: none; border-radius: 4px; cursor: pointer;">
                                                    Registrar
                                                </button>
                                                <div id="registrar-form-{{ $p->id }}" style="display:none; margin-top: 8px;">
                                                    <form method="POST" action="{{ route('pagamento.registrar', $p->id) }}">
                                                        @csrf
                                                        <textarea name="obs" rows="2" placeholder="Observação (opcional)" style="width: 100%; margin-bottom: 5px;"></textarea>
                                                        <input type="date" name="data_registro" value="{{ now()->format('Y-m-d') }}" style="margin-bottom: 5px;" />
                                                        <button type="submit" style="background: #28a745; color: white; border: none; padding: 5px 10px; border-radius: 4px;">Confirmar</button>
                                                    </form>
                                                </div>
                                            @endif
  <a href="{{ route('pedido.visualizar', $pedido->id) }}"
       style="padding: 5px 10px; background-color: #17a2b8; color: white; border-radius: 4px; text-decoration: none;"
       title="Ver pedido completo">
        Ver Pedido
    </a>
                                            <form action="{{ route('pagamento.destroy', $p->id) }}" method="POST" style="display:inline-block; margin-left: 5px;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Tem certeza que deseja excluir este pagamento?');"
                                                    style="padding: 4px 10px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 0.85rem;">
                                                    Excluir
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Botão único para adicionar pagamento do pedido -->
                        <div style="margin-top: 12px; text-align: right;">
                            <button
                                onclick="openPagamentoModal({{ $pedido->id }}, '{{ addslashes($pedido->cliente->nome ?? '') }}')"
                                style="padding: 8px 14px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer;"
                                title="Adicionar Novo Pagamento"
                            >
                                + Novo Pagamento
                            </button>
                            
                        </div>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Nenhum pagamento encontrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Modal Novo Pagamento -->
    <div id="modal-pagamento" style="display:none; position: fixed; top:0; left:0; width: 100%; height: 100%; 
        background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999;">
        <div style="background: white; padding: 20px; border-radius: 8px; max-width: 400px; width: 90%; position: relative;">
            <h2 style="margin-top: 0;">Novo Pagamento</h2>
            <form method="POST" action="{{ route('pagamento.store') }}">
                @csrf
                <input type="hidden" name="pedido_id" id="pedido_id_modal">

                <div style="margin-bottom: 10px;">
                    <label for="cliente_nome" style="font-weight: 600;">Cliente:</label>
                    <input type="text" id="cliente_nome_modal" disabled style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 10px;">
                    <label for="valor" style="font-weight: 600;">Valor:</label>
                    <input type="number" step="0.01" name="valor" id="valor" required style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 10px;">
                    <label for="data" style="font-weight: 600;">Data:</label>
                    <input type="date" name="data" id="data" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 10px;">
                    <label for="forma" style="font-weight: 600;">Forma:</label>
                    <select name="forma" id="forma" required style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">Selecione</option>
                        <option value="PIX">PIX</option>
                        <option value="DEBITO">DEBITO</option>
                        <option value="DINHEIRO">DINHEIRO</option>
                        <option value="CREDITO À VISTA">CREDITO À VISTA</option>
                        <option value="CREDITO PARCELADO">CREDITO PARCELADO</option>
<option value="NA ENTREGA">NA ENTREGA</option>
<option value="A PRAZO">A PRAZO</option>
                        <option value="BOLETO">BOLETO</option>
                        <option value="CHEQUE">CHEQUE</option>
                        <option value="OUTROS">OUTROS</option>
                    </select>
                </div>

                <div style="margin-bottom: 10px;">
                    <label for="obs" style="font-weight: 600;">Observação:</label>
                    <textarea name="obs" id="obs" rows="3" style="width: 100%; padding: 6px; border: 1px solid #ccc; border-radius: 4px;"></textarea>
                </div>

                <div style="text-align: right;">
                    <button type="button" onclick="closePagamentoModal()" style="margin-right: 10px; padding: 6px 12px; border: none; background: #6c757d; color: white; border-radius: 4px; cursor: pointer;">Cancelar</button>
                    <button type="submit" style="background-color: #28a745; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePagamentos(id) {
            const row = document.getElementById('pagamentos-' + id);
            row.style.display = (row.style.display === 'none') ? 'table-row' : 'none';
        }

        function toggleRegistrar(id) {
            const form = document.getElementById('registrar-form-' + id);
            form.style.display = (form.style.display === 'none') ? 'block' : 'none';
        }

        function openPagamentoModal(pedidoId, clienteNome) {
            document.getElementById('pedido_id_modal').value = pedidoId;
            document.getElementById('cliente_nome_modal').value = clienteNome;
            document.getElementById('modal-pagamento').style.display = 'flex';
        }

        function closePagamentoModal() {
            document.getElementById('modal-pagamento').style.display = 'none';
        }
    </script>
    <script>
    function togglePagamentos(id) {
        const row = document.getElementById('pagamentos-' + id);
        row.style.display = (row.style.display === 'none') ? 'table-row' : 'none';
    }

    function toggleRegistrar(id) {
        const form = document.getElementById('registrar-form-' + id);
        form.style.display = (form.style.display === 'none') ? 'block' : 'none';
    }

    function openPagamentoModal(pedidoId, clienteNome) {
        document.getElementById('pedido_id_modal').value = pedidoId;
        document.getElementById('cliente_nome_modal').value = clienteNome;
        document.getElementById('modal-pagamento').style.display = 'flex';
    }

    function closePagamentoModal() {
        document.getElementById('modal-pagamento').style.display = 'none';
    }

    // Toggle dos meses (EXPANDIR/RECOLHER)
function togglePeriodo() {
    const container = document.getElementById('periodoContainer');
    const seta = document.getElementById('seta');
    const isVisible = container.style.display === 'block';

    container.style.display = isVisible ? 'none' : 'block';
    seta.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(90deg)';
}
function toggleForma() {
    const container = document.getElementById('formaContainer');
    const seta = document.getElementById('seta-forma');
    const isVisible = container.style.display === 'block';

    container.style.display = isVisible ? 'none' : 'block';
    seta.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(90deg)';
}

</script>

</x-app-layout>
