<div class="container">
    <h2>Visualização do Pedido</h2>

    <h3>Cliente</h3>
    <ul>
        <li><strong>Nome:</strong> {{ $pedido->cliente->nome }}</li>
        <li><strong>Telefone:</strong> {{ $pedido->cliente->telefone }}</li>
        <li><strong>Endereço:</strong> {{ $pedido->cliente->endereco }}</li>
        <li><strong>CPF:</strong> {{ $pedido->cliente->cpf }}</li>
        <li><strong>Email:</strong> {{ $pedido->cliente->email }}</li>
    </ul>

    <h3>Pedido</h3>
    <ul>
        <li><strong>Data:</strong> {{ $pedido->data }}</li>
        <li><strong>Data de Retirada:</strong> {{ $pedido->data_retirada }}</li>
        <li><strong>Prazo:</strong> {{ $pedido->prazo }}</li>
    </ul>
<button type="button" class="btn btn-outline-primary mb-3" data-bs-toggle="modal" data-bs-target="#editarClientePedidoModal">
    ✏️ Editar Cliente/Pedido
</button>

    <h3>Itens</h3>
    @foreach($pedido->items as $item)
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <p><strong>Nome do Item:</strong> {{ $item->nomeItem }}</p>
            <p><strong>Material:</strong> {{ $item->material }}</p>
            <p><strong>Metragem:</strong> {{ $item->metragem }} m</p>
            <p><strong>Especificações:</strong> {{ $item->especifi }}</p>

            <h4>Serviços Terceirizados</h4>
            @forelse($item->terceirizadas as $terc)
                <p>- {{ $terc->tipoServico }} (Obs: {{ $terc->obs }})</p>
            @empty
                <p>Sem serviços terceirizados</p>
            @endforelse

            <button type="button" class="btn btn-sm btn-warning mt-2" data-bs-toggle="modal" data-bs-target="#editarItemModal{{ $item->id }}">
                ✏️ Editar Item
            </button>
            <button type="button" class="btn btn-sm btn-success mt-2 ms-2" data-bs-toggle="modal" data-bs-target="#adicionarTerceirizadaModal{{ $item->id }}">
    + Adicionar Terceirizada
</button>

<!-- Modal para adicionar terceirizada -->
<div class="modal fade" id="adicionarTerceirizadaModal{{ $item->id }}" tabindex="-1" aria-labelledby="adicionarTerceirizadaModalLabel{{ $item->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('terceirizada.store') }}">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">
            <input type="hidden" name="andamento" value="em espera">
            <input type="hidden" name="valor" value="0">
            <input type="hidden" name="statusPg" value="Pendente">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adicionarTerceirizadaModalLabel{{ $item->id }}">Adicionar Serviço Terceirizado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tipoServico{{ $item->id }}" class="form-label">Tipo de Serviço</label>
                        <select id="tipoServico{{ $item->id }}" name="tipoServico" class="form-select" required>
                            <option value="">Selecione</option>
                            <option value="Impermeabilizar">Impermeabilizar</option>
                            <option value="Higienizar">Higienizar</option>
                            <option value="Pintar">Pintar</option>
                            <option value="Invernizar">Invernizar</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="obs{{ $item->id }}" class="form-label">Observação</label>
                        <textarea id="obs{{ $item->id }}" name="obs" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

        </div>
<!-- Modal Editar Cliente e Pedido -->
<div class="modal fade" id="editarClientePedidoModal" tabindex="-1" aria-labelledby="editarClientePedidoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('pedido.update', $pedido->id) }}">
            @csrf
            @method('PUT')

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Cliente e Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body row">
                    <!-- Cliente -->
                    <div class="col-md-6">
                        <h5>Cliente</h5>
                        <div class="mb-3">
                            <label>Nome</label>
                            <input type="text" name="cliente[nome]" class="form-control" value="{{ $pedido->cliente->nome }}">
                        </div>
                        <div class="mb-3">
                            <label>Telefone</label>
                            <input type="text" name="cliente[telefone]" class="form-control" value="{{ $pedido->cliente->telefone }}">
                        </div>
                        <div class="mb-3">
                            <label>Endereço</label>
                            <input type="text" name="cliente[endereco]" class="form-control" value="{{ $pedido->cliente->endereco }}">
                        </div>
                        <div class="mb-3">
                            <label>CPF</label>
                            <input type="text" name="cliente[cpf]" class="form-control" value="{{ $pedido->cliente->cpf }}">
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="cliente[email]" class="form-control" value="{{ $pedido->cliente->email }}">
                        </div>
                    </div>

<!-- Pedido -->
<div class="col-md-6">
    <h5>Pedido</h5>
    <div class="mb-3">
        <label>Data do Pedido</label>
        <input type="date" name="data" class="form-control" value="{{ $pedido->data }}">
    </div>
    <div class="mb-3">
        <label>Data de Retirada</label>
        <input type="date" name="data_retirada" class="form-control" value="{{ $pedido->data_retirada }}">
    </div>
    <div class="mb-3">
        <label>Prazo</label>
        <input type="date" name="prazo" class="form-control" value="{{ $pedido->prazo }}">
    </div>
</div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

        <!-- Modal -->
        <div class="modal fade" id="editarItemModal{{ $item->id }}" tabindex="-1" aria-labelledby="editarItemModalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('item.update', $item->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nome do Item</label>
                                <input type="text" class="form-control" name="nomeItem" value="{{ $item->nomeItem }}">
                            </div>
                            <div class="mb-3">
                                <label>Material</label>
                                <input type="text" class="form-control" name="material" value="{{ $item->material }}">
                            </div>
                            <div class="mb-3">
                                <label>Metragem</label>
                                <input type="number" step="0.01" class="form-control" name="metragem" value="{{ $item->metragem }}">
                            </div>
                            <div class="mb-3">
                                <label>Especificações</label>
                                <textarea class="form-control" name="especifi">{{ $item->especifi }}</textarea>
                            </div>

                            <h5 class="mt-4">Serviços Terceirizados</h5>
                            @foreach ($item->terceirizadas as $i => $terc)
                                <div class="terceirizada" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
                                    <input type="hidden" name="terceirizadas[{{ $i }}][id]" value="{{ $terc->id }}">

                                    <label>Tipo de Serviço</label>
<select name="terceirizadas[{{ $i }}][tipoServico]" class="form-select">
    <option value="Impermeabilizar" {{ $terc->tipoServico == 'Impermeabilizar' ? 'selected' : '' }}>Impermeabilizar</option>
    <option value="Higienizar" {{ $terc->tipoServico == 'Higienizar' ? 'selected' : '' }}>Higienizar</option>
    <option value="Pintar" {{ $terc->tipoServico == 'Pintar' ? 'selected' : '' }}>Pintar</option>
    <option value="Invernizar" {{ $terc->tipoServico == 'Invernizar' ? 'selected' : '' }}>Invernizar</option>
    <option value="Outros" {{ $terc->tipoServico == 'Outros' ? 'selected' : '' }}>Outros</option>
</select>

                                    <label>Observação</label>
                                    <input type="text" name="terceirizadas[{{ $i }}][obs]" class="form-control" value="{{ $terc->obs }}">

                                    <button type="button" class="btn btn-danger mt-2" onclick="deletarTerceirizada({{ $terc->id }})">🗑️ Excluir</button>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Salvar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <h3>Imagens</h3>
    <div style="display: flex; flex-wrap: wrap;">
@foreach($pedido->imagens as $imagem)
    <div style="position: relative; display: inline-block; margin: 10px;">
        <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do pedido" style="max-width: 200px; border-radius: 4px;">
        <form method="POST" action="{{ route('pedido.imagem.destroy', $imagem->id) }}" style="position: absolute; top: 5px; right: 5px;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir essa imagem?')">✖</button>
        </form>
    </div>
@endforeach
       <button type="button" class="btn btn-sm btn-outline-success mb-3 px-2 py-1" style="font-size: 0.8rem;" data-bs-toggle="modal" data-bs-target="#adicionarImagemModal">
    + Adicionar Imagem
</button>


<!-- Modal para adicionar imagens -->
<div class="modal fade" id="adicionarImagemModal" tabindex="-1" aria-labelledby="adicionarImagemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('pedido.imagem.store', $pedido->id) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adicionarImagemModalLabel">Adicionar Imagem ao Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
 <input type="file" name="imagens[]" multiple accept="image/*" required class="form-control" />                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </form>
    </div>
</div>

    </div>

    <div style="border: 2px solid #000; padding: 15px; margin: 20px 0; border-radius: 8px;">
        <h3>Valor do Pedido</h3>
        <p><strong>Total:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</p>
        <p><strong>Valor Restante:</strong> R$ {{ number_format($pedido->valor_resta, 2, ',', '.') }}</p>
    </div>

    <h3>Pagamentos</h3>
    @foreach ($pedido->pagamentos as $pagamento)
        <div class="pagamento">
            <p><strong>Data:</strong> {{ $pagamento->data }}</p>
            <p><strong>Valor:</strong> R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</p>
            <p><strong>Forma:</strong> {{ $pagamento->forma }}</p>
            <p><strong>Status:</strong> {{ $pagamento->status }}</p>

            @if ($pagamento->status === 'EM ABERTO')
                <form action="{{ route('pagamento.registrar', $pagamento->id) }}" method="POST">
                    @csrf
                    <input type="text" name="obs" placeholder="Observação (opcional)" class="form-control" />
                    <button type="submit" class="btn btn-success mt-2" onclick="return confirm('Confirmar pagamento?')">✅ Registrar Pagamento</button>
                </form>
            @endif

            @if ($pagamento->data_registro)
                <p><strong>Registrado em:</strong> {{ \Carbon\Carbon::parse($pagamento->data_registro)->format('d/m/Y') }}</p>
            @endif
            <hr>
        </div>
    @endforeach

    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
        <a href="{{ route('pagamento.create', ['cliente_id' => $pedido->cliente->id]) }}" class="btn btn-success">+ Novo Pagamento</a>
        <a href="{{ route('pagamento.index', ['cliente_id' => $pedido->cliente->id]) }}" class="btn btn-info">Ir para Pagamentos</a>
    </div>

    <a href="{{ route('pedidos.imprimirviacompleta', $pedido->id) }}" target="_blank" class="btn btn-primary">🖨️ Imprimir Via</a>
</div>

<!-- Formulário invisível para DELETE -->
<form id="form-delete-terceirizada" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function deletarTerceirizada(id) {
        if (confirm('Tem certeza que deseja excluir este serviço terceirizado?')) {
            const form = document.getElementById('form-delete-terceirizada');
            form.action = `/terceirizada/${id}`;
            form.submit();
        }
    }
</script>
