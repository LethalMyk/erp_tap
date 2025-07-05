<!-- resources/views/agendamentos/index.blade.php -->

<x-app-layout>
<a href="{{ route('agendamentos.calendario') }}" class="btn btn-info mb-3">📅 Ver Calendário</a>

<div class="container">
    <h2>📅 Agenda</h2>

    <div class="mb-4">
        <a href="{{ route('agendamentos.create') }}" class="btn btn-primary">➕ Novo Agendamento</a>
    </div>

    <h4>📌 Hoje ({{ now()->format('d/m/Y') }})</h4>
    @include('agendamentos.partials.lista', ['agendamentos' => $agendamentosHoje])

    <h4 class="mt-4">📆 Esta Semana</h4>
    @include('agendamentos.partials.lista', ['agendamentos' => $agendamentosSemana])

    <h4 class="mt-4">📆 Próximas 2 Semanas</h4>
    @include('agendamentos.partials.lista', ['agendamentos' => $agendamentosProximasSemanas])

    <h4 class="mt-4">🗓️ Todos os Futuros</h4>
    @include('agendamentos.partials.lista', ['agendamentos' => $agendamentosFuturos])

    <h4 class="mt-4">📁 Anteriores</h4>
    @include('agendamentos.partials.lista', ['agendamentos' => $agendamentosPassados])
</div>
</x-app-layout>
