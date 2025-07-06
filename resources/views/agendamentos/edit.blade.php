<!-- resources/views/agendamentos/create.blade.php -->

<x-app-layout>
<div class="container">
    <h2>Editar Agendamento</h2>

    <form action="{{ route('agendamentos.update', $agendamento->id) }}" method="POST">
        @csrf
        @method('PUT')

        @include('agendamentos.partials.form', ['agendamento' => $agendamento])

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="{{ route('agendamentos.index') }}" class="btn btn-secondary">Voltar</a>
    </form>

    
</div>
</x-app-layout>

