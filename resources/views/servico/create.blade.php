 <x-app-layout>
   <h2>Novo Serviço</h2>
<form action="{{ route('servico.store') }}" method="POST">
    @csrf
    <label>Profissional:</label>
    <select name="profissional_id">
        @foreach($profissionais as $profissional)
            <option value="{{ $profissional->id }}">{{ $profissional->nome }}</option>
        @endforeach
    </select>

    <label>Pedido:</label>
    <select name="pedido_id">
        @foreach($pedidos as $pedido)
            <option value="{{ $pedido->id }}">{{ $pedido->id }}</option>
        @endforeach
    </select>

    <label>Data Início:</label>
    <input type="date" name="data_inicio" required>

    <label>Dificuldade:</label>
    <input type="text" name="dificuldade" required>

    <label>Data Previsão:</label>
    <input type="date" name="data_previsao">

    <label>Observações:</label>
    <textarea name="obs"></textarea>

    <button type="submit">Salvar</button>
</form>
</x-app-layout>
