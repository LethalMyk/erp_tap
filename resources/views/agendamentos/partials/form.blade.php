@php $isEdit = isset($agendamento); @endphp

<div class="mb-3">
    <label>Tipo</label>
    <select name="tipo" class="form-select">
        @foreach(['entrega', 'retirada', 'assistencia', 'orcamento'] as $tipo)
            <option value="{{ $tipo }}" @if($isEdit && $agendamento->tipo == $tipo) selected @endif>{{ ucfirst($tipo) }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Data</label>
    <input type="date" name="data" class="form-control" value="{{ $isEdit ? $agendamento->data : old('data') }}">
</div>

<div class="mb-3">
    <label>Horário</label>
    <input type="time" name="horario" class="form-control" value="{{ $isEdit ? $agendamento->horario : old('horario') }}">
</div>

<div class="mb-3">
    <label>Nome do Cliente</label>
    <input type="text" name="nome_cliente" class="form-control" value="{{ $isEdit ? $agendamento->nome_cliente : old('nome_cliente') }}">
</div>

<div class="mb-3">
    <label>Endereço</label>
    <textarea name="endereco" class="form-control">{{ $isEdit ? $agendamento->endereco : old('endereco') }}</textarea>
</div>
<div class="mb-3">
    <label>Telefone</label>
    <input type="text" name="telefone" class="form-control" value="{{ $isEdit ? $agendamento->telefone : old('telefone') }}">
</div>

<div class="mb-3">
    <label>Itens</label>
    <textarea name="itens" class="form-control">{{ $isEdit ? $agendamento->itens : old('itens') }}</textarea>
</div>

<div class="mb-3">
    <label>Observações</label>
    <textarea name="observacao" class="form-control">{{ $isEdit ? $agendamento->observacao : old('observacao') }}</textarea>
</div>

<div class="mb-3">
    <label>Status</label>
    <select name="status" class="form-select">
        @foreach(['pendente', 'realizado', 'cancelado'] as $status)
            <option value="{{ $status }}" @if($isEdit && $agendamento->status == $status) selected @endif>{{ ucfirst($status) }}</option>
        @endforeach
    </select>
</div>
