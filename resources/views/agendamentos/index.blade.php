<x-app-layout>
    <a href="{{ route('agendamentos.calendario') }}" class="btn btn-info mb-3">📅 Ver Calendário</a>

    <div class="container">

        <div class="mb-4 d-flex gap-2 flex-wrap">
            <a href="{{ route('agendamentos.create') }}" class="btn btn-primary abrir-novo-agendamento">➕ Novo Agendamento</a>
            <button id="btnMostrarPassados" class="btn btn-outline-secondary">📁 Mostrar Anteriores</button>
        </div>

        <h2>📅 Agenda</h2>

        {{-- Checkboxes para ocultar/mostrar --}}
        <div class="form-check form-check-inline mb-4">
            <input type="checkbox" class="form-check-input" id="toggleGerais" checked>
            <label class="form-check-label" for="toggleGerais">Mostrar Agendamentos</label>
        </div>
        <div class="form-check form-check-inline mb-4">
            <input type="checkbox" class="form-check-input" id="toggleOrcamentos" checked>
            <label class="form-check-label" for="toggleOrcamentos">Mostrar Orçamentos</label>
        </div>

        {{-- Loop dos próximos 15 dias --}}
        @for ($i = 0; $i <= 14; $i++)
            @php
                $dataFormatada = \Carbon\Carbon::today()->addDays($i)->format('d/m/Y');
                $tituloDia = match ($i) {
                    0 => '📌 Hoje',
                    1 => '📅 Amanhã',
                    default => '📅 ' . \Carbon\Carbon::today()->addDays($i)->format('d/m/Y'),
                };
            @endphp

@php
    $dataCarbon = \Carbon\Carbon::today()->addDays($i)->locale('pt_BR');
    $dataFormatada = $dataCarbon->translatedFormat('d/m/Y l'); // 09/07/2025 Quarta-feira

    $tituloDia = match ($i) {
        0 => '📌 Hoje',
        1 => '📅 Amanhã',
        default => '📅 ' . $dataFormatada,
    };
    $dataIso = $dataCarbon->format('Y-m-d');
@endphp
<h4 class="mt-4 dia-agendamento" data-data="{{ $dataIso }}" style="cursor:pointer;">
    {{ $tituloDia }} ({{ $dataFormatada }}) - Entregas e Retiradas
</h4>

            <div class="agendamentos-gerais" id="geraisDia{{ $i }}">
                @include('agendamentos.partials.lista-geral', ['agendamentos' => $agendamentosPorDia[$i]])
            </div>
            <h4 class="mt-4 dia-agendamento" data-data="{{ $dataIso }}" data-tipo="orcamento" style="cursor:pointer;">
    {{ $tituloDia }} ({{ $dataFormatada }}) - Orçamentos
</h4>

            <div class="orcamentos" id="orcamentosDia{{ $i }}">
                @include('agendamentos.partials.lista-orcamentos', ['orcamentos' => $orcamentosPorDia[$i]])
            </div>
            <hr>
        @endfor

        {{-- Agendamentos Futuros além dos 15 dias --}}
        <h4 class="mt-4">🗓️ Demais Futuros</h4>
        <div class="agendamentos-gerais" id="geraisFuturos">
            @include('agendamentos.partials.lista-geral', ['agendamentos' => $agendamentosFuturos])
        </div>
        <div class="orcamentos" id="orcamentosFuturos">
            @include('agendamentos.partials.lista-orcamentos', ['orcamentos' => $orcamentosFuturos])
        </div>

        {{-- Passados (inicialmente ocultos) --}}
        <div id="blocoPassados" style="display: none;">
            <h4 class="mt-4">📁 Anteriores</h4>
            <div class="agendamentos-gerais" id="geraisPassados">
                @include('agendamentos.partials.lista-geral', ['agendamentos' => $agendamentosPassados])
            </div>
            <div class="orcamentos" id="orcamentosPassados">
                @include('agendamentos.partials.lista-orcamentos', ['orcamentos' => $orcamentosPassados])
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="POST" id="formEditar" class="modal-content" action="">
            @csrf
            <input type="hidden" name="_method" id="form_method" value="POST" />
            {{-- INPUT OCULTO PARA REDIRECIONAMENTO --}}
            <input type="hidden" name="redirect_to" value="lista">

            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">✏️ Editar Agendamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_id" name="agendamento_id">

                <div class="mb-3">
                    <label>Tipo</label>
                    <select name="tipo" class="form-select" id="edit_tipo" required>
                        <option value="entrega">Entrega</option>
                        <option value="retirada">Retirada</option>
                        <option value="assistencia">Assistência</option>
                        <option value="orcamento">Orçamento</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Data</label>
                    <input type="date" name="data" class="form-control" id="edit_data" required>
                </div>
                <div class="mb-3">
                    <label>Horário</label>
                    <input type="time" name="horario" class="form-control" id="edit_horario" required>
                </div>
                <div class="mb-3">
                    <label>Nome do Cliente</label>
                    <input type="text" name="nome_cliente" class="form-control" id="edit_nome_cliente" required>
                </div>
                <div class="mb-3">
                    <label>Endereço</label>
                    <textarea name="endereco" class="form-control" id="edit_endereco" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Telefone</label>
                    <input type="text" name="telefone" class="form-control" id="edit_telefone">
                </div>
                <div class="mb-3">
                    <label>Itens</label>
                    <textarea name="itens" class="form-control" id="edit_itens"></textarea>
                </div>
                <div class="mb-3">
                    <label>Observação</label>
                    <textarea name="observacao" class="form-control" id="edit_observacao"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Salvar Alterações</button>
            </div>
        </form>
      </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.getElementById('toggleGerais').addEventListener('change', function() {
            const show = this.checked;
            document.querySelectorAll('.agendamentos-gerais').forEach(el => {
                el.style.display = show ? 'block' : 'none';
            });
        });

        document.getElementById('toggleOrcamentos').addEventListener('change', function() {
            const show = this.checked;
            document.querySelectorAll('.orcamentos').forEach(el => {
                el.style.display = show ? 'block' : 'none';
            });
        });

        document.getElementById('btnMostrarPassados').addEventListener('click', function () {
            const bloco = document.getElementById('blocoPassados');
            const visivel = bloco.style.display === 'block';

            bloco.style.display = visivel ? 'none' : 'block';
            this.innerText = visivel ? '📁 Mostrar Anteriores' : '📁 Ocultar Anteriores';
        });

        // Dblclick: abrir modal com dados para edição
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-agendamento]').forEach(function(card) {
                card.addEventListener('dblclick', function () {
                    const agendamento = JSON.parse(this.dataset.agendamento);

                    const form = document.getElementById('formEditar');
                    form.action = `/agendamentos/${agendamento.id}`;
                    document.getElementById('form_method').value = 'PUT';

                    // Preenche os campos
                    document.getElementById('edit_id').value = agendamento.id || '';
                    document.getElementById('edit_tipo').value = agendamento.tipo || '';
                    document.getElementById('edit_data').value = agendamento.data || '';
                    document.getElementById('edit_horario').value = agendamento.horario || '';
                    document.getElementById('edit_nome_cliente').value = agendamento.nome_cliente || '';
                    document.getElementById('edit_endereco').value = agendamento.endereco || '';
                    document.getElementById('edit_telefone').value = agendamento.telefone || '';
                    document.getElementById('edit_itens').value = agendamento.itens || '';
                    document.getElementById('edit_observacao').value = agendamento.observacao || '';

                    document.getElementById('modalEditarLabel').innerText = '✏️ Editar ' + capitalizeFirstLetter(agendamento.tipo);

                    new bootstrap.Modal(document.getElementById('modalEditar')).show();
                });
            });
        });

        // Função para capitalizar a primeira letra
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        // Abrir modal para novo agendamento
        document.querySelectorAll('.abrir-novo-agendamento').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const form = document.getElementById('formEditar');
                form.reset();
                document.getElementById('edit_id').value = '';
                document.getElementById('edit_tipo').value = 'entrega';
                form.action = "{{ route('agendamentos.store') }}";
                document.getElementById('form_method').value = 'POST';
                document.getElementById('modalEditarLabel').innerText = '➕ Novo Agendamento';

                new bootstrap.Modal(document.getElementById('modalEditar')).show();
            });
        });

        // Abrir modal para novo orçamento
        document.querySelectorAll('.abrir-novo-orcamento').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                const form = document.getElementById('formEditar');
                form.reset();
                document.getElementById('edit_id').value = '';
                document.getElementById('edit_tipo').value = 'orcamento';
                form.action = "{{ route('agendamentos.store') }}";
                document.getElementById('form_method').value = 'POST';
                document.getElementById('modalEditarLabel').innerText = '➕ Novo Orçamento';

                new bootstrap.Modal(document.getElementById('modalEditar')).show();
            });
        });

        // Atualiza título do modal conforme tipo selecionado
        document.addEventListener('DOMContentLoaded', function () {
            const tipoSelect = document.getElementById('edit_tipo');
            const tituloModal = document.getElementById('modalEditarLabel');

            if (tipoSelect && tituloModal) {
                tipoSelect.addEventListener('change', function () {
                    const tipo = tipoSelect.value;
                    switch (tipo) {
                        case 'orcamento':
                            tituloModal.innerText = '✏️ Editar Orçamento';
                            break;
                        case 'entrega':
                            tituloModal.innerText = '✏️ Editar Entrega';
                            break;
                        case 'retirada':
                            tituloModal.innerText = '✏️ Editar Retirada';
                            break;
                        case 'assistencia':
                            tituloModal.innerText = '✏️ Editar Assistência';
                            break;
                        default:
                            tituloModal.innerText = '✏️ Editar Agendamento';
                    }
                });
            }
        });

        document.querySelectorAll('.dia-agendamento').forEach(function(element) {
    element.addEventListener('click', function() {
        const dataSelecionada = this.getAttribute('data-data');
        const tipoSelecionado = this.getAttribute('data-tipo') || 'entrega'; // <- aqui pegamos o tipo dinamicamente

        const form = document.getElementById('formEditar');
        form.reset();

        document.getElementById('form_method').value = 'POST';
        form.action = "{{ route('agendamentos.store') }}";

        document.getElementById('edit_data').value = dataSelecionada;
        document.getElementById('edit_tipo').value = tipoSelecionado;
        document.getElementById('edit_id').value = '';
        document.getElementById('edit_nome_cliente').value = '';
        document.getElementById('edit_endereco').value = '';
        document.getElementById('edit_telefone').value = '';
        document.getElementById('edit_itens').value = '';
        document.getElementById('edit_observacao').value = '';

        // Ajusta título do modal de acordo com o tipo
        let tituloModal = '➕ Novo Agendamento';
        if (tipoSelecionado === 'orcamento') {
            tituloModal = '➕ Novo Orçamento';
        } else if (tipoSelecionado === 'entrega') {
            tituloModal = '➕ Nova Entrega';
        } else if (tipoSelecionado === 'retirada') {
            tituloModal = '➕ Nova Retirada';
        } else if (tipoSelecionado === 'assistencia') {
            tituloModal = '➕ Nova Assistência';
        }

        document.getElementById('modalEditarLabel').innerText = tituloModal;

        new bootstrap.Modal(document.getElementById('modalEditar')).show();
    });
});

    </script>
</x-app-layout>
