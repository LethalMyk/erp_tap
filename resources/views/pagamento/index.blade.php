<x-app-layout>
    <!-- ... seu formulário e tabela acima ficam iguais ... -->

    <table style="width: 100%; border-collapse: collapse;" id="tabela-pedidos">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Endereço</th>
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
                    <td>R$ {{ number_format($pedido->valor ?? 0, 2, ',', '.') }}</td>
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
</x-app-layout>
