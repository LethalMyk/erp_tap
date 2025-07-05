<!-- resources/views/agendamentos/create.blade.php -->

<x-app-layout>
<div class="container">
    <h2>Novo Agendamento</h2>

    <form action="{{ route('agendamentos.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Tipo</label>
            <select name="tipo" class="form-select">
                <option value="entrega">Entrega</option>
                <option value="retirada">Retirada</option>
                <option value="assistencia">Assistência</option>
                <option value="orcamento">Orçamento</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Data</label>
<input type="date" name="data" class="form-control" value="{{ $data ?? old('data') }}">
        </div>

        <div class="mb-3">
            <label>Horário</label>
<input type="time" name="horario" class="form-control" value="{{ $horario ?? old('horario') }}">        </div>

        <div class="mb-3">
            <label>Nome do Cliente</label>
            <input type="text" name="nome_cliente" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Endereço</label>
            <textarea name="endereco" class="form-control" required></textarea>
        </div>
<div class="mb-3">
    <label for="telefone" class="form-label">Telefone</label>
    <input type="text" class="form-control" name="telefone" id="telefone" value="{{ old('telefone', $agendamento->telefone ?? '') }}">
</div>

        <div class="mb-3">
            <label>Itens</label>
            <textarea name="itens" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Observação</label>
            <textarea name="observacao" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Agendar</button>
    </form>
</div>
</x-app-layout>

