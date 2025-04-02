<x-app-layout>

   <h2>Editar Serviço</h2>

<form action="{{ route('servico.update', $servico->id) }}" method="POST">
    @csrf
    @method('PUT')

    <label>Profissional:</label>
    <select name="profissional_id">
        @foreach($profissionais as $profissional)
            <option value="{{ $profissional->id }}" {{ $servico->profissional_id == $profissional->id ? 'selected' : '' }}>
                {{ $profissional->nome }}
            </option>
        @endforeach
    </select>

    <label>Pedido:</label>
    <select name="pedido_id">
        @foreach($pedidos as $pedido)
            <option value="{{ $pedido->id }}" {{ $servico->pedido_id == $pedido->id ? 'selected' : '' }}>
                {{ $pedido->id }}
            </option>
        @endforeach
    </select>

    <label>Data Início:</label>
    <input type="date" name="data_inicio" value="{{ $servico->data_inicio }}" required>

    <label>Dificuldade:</label>
    <input type="text" name="dificuldade" value="{{ $servico->dificuldade }}" required>

    <label>Data Previsão:</label>
    <input type="date" name="data_previsao" value="{{ $servico->data_previsao }}">

    <label>Data Término:</label>
    <input type="date" name="data_termino" value="{{ $servico->data_termino }}">

    <label>Observações:</label>
    <textarea name="obs">{{ $servico->obs }}</textarea>

    <button type="submit" class="btn btn-success">Salvar</button>
</form>

<a href="{{ route('servico.index') }}" class="btn btn-secondary">Voltar</a>
</x-app-layout>

