<x-app-layout>
    <a href="{{ route('agendamentos.calendario') }}" class="btn btn-info mb-3">📅 Ver Calendário</a>

    <div class="container">

        <div class="mb-4 d-flex gap-2 flex-wrap">
            <a href="{{ route('agendamentos.create') }}" class="btn btn-primary">➕ Novo Agendamento</a>
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

        {{-- HOJE --}}
        <h4>📌 Hoje ({{ now()->format('d/m/Y') }})</h4>
        <div class="agendamentos-gerais" id="geraisHoje">
            @include('agendamentos.partials.lista-geral', ['agendamentos' => $agendamentosHoje])
        </div>
        <div class="orcamentos" id="orcamentosHoje">
            @include('agendamentos.partials.lista-orcamentos', ['orcamentos' => $orcamentosHoje])
        </div>

        {{-- ESTA SEMANA --}}
        <h4 class="mt-4">📆 Esta Semana</h4>
        <div class="agendamentos-gerais" id="geraisSemana">
            @include('agendamentos.partials.lista-geral', ['agendamentos' => $agendamentosSemana])
        </div>
        <div class="orcamentos" id="orcamentosSemana">
            @include('agendamentos.partials.lista-orcamentos', ['orcamentos' => $orcamentosSemana])
        </div>

        {{-- PRÓXIMAS SEMANAS --}}
        <h4 class="mt-4">📆 Próximas 2 Semanas</h4>
        <div class="agendamentos-gerais" id="geraisProximasSemanas">
            @include('agendamentos.partials.lista-geral', ['agendamentos' => $agendamentosProximasSemanas])
        </div>
        <div class="orcamentos" id="orcamentosProximasSemanas">
            @include('agendamentos.partials.lista-orcamentos', ['orcamentos' => $orcamentosProximasSemanas])
        </div>

        {{-- FUTUROS --}}
        <h4 class="mt-4">🗓️ Todos os Futuros</h4>
        <div class="agendamentos-gerais" id="geraisFuturos">
            @include('agendamentos.partials.lista-geral', ['agendamentos' => $agendamentosFuturos])
        </div>
        <div class="orcamentos" id="orcamentosFuturos">
            @include('agendamentos.partials.lista-orcamentos', ['orcamentos' => $orcamentosFuturos])
        </div>

        {{-- PASSADOS - Inicialmente ocultos --}}
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
        <form method="POST" id="formEditar" class="modal-content">
            @csrf
            @method('PUT')

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

        // Dblclick: abrir modal com dados
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-agendamento]').forEach(function(card) {
                card.addEventListener('dblclick', function () {
                    const agendamento = JSON.parse(this.dataset.agendamento);

                    // Define a rota de atualização
                    document.getElementById('formEditar').action = `/agendamentos/${agendamento.id}`;

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

                    new bootstrap.Modal(document.getElementById('modalEditar')).show();
                });
            });
        });
    </script>
</x-app-layout>
