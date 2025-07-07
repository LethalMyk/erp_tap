<x-app-layout>
    <a href="{{ route('agendamentos.calendario') }}" class="btn btn-info mb-3">📅 Ver Calendário</a>

    <div class="container">
        <h2>📅 Agenda</h2>

        <div class="mb-4">
            <a href="{{ route('agendamentos.create') }}" class="btn btn-primary">➕ Novo Agendamento</a>
        </div>

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

        {{-- PASSADOS --}}
        <h4 class="mt-4">📁 Anteriores</h4>
        <div class="agendamentos-gerais" id="geraisPassados">
            @include('agendamentos.partials.lista-geral', ['agendamentos' => $agendamentosPassados])
        </div>
        <div class="orcamentos" id="orcamentosPassados">
            @include('agendamentos.partials.lista-orcamentos', ['orcamentos' => $orcamentosPassados])
        </div>
    </div>

    {{-- Script para mostrar/ocultar --}}
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
    </script>
</x-app-layout>
