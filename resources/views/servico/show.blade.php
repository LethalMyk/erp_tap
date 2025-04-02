<x-app-layout>
<h2>Detalhes do Serviço</h2>

<p><strong>ID:</strong> {{ $servico->id }}</p>
<p><strong>Profissional:</strong> {{ $servico->profissional->nome }}</p>
<p><strong>Pedido:</strong> {{ $servico->pedido->id }}</p>
<p><strong>Data Início:</strong> {{ $servico->data_inicio }}</p>
<p><strong>Data Previsão:</strong> {{ $servico->data_previsao }}</p>
<p><strong>Data Término:</strong> {{ $servico->data_termino ?? 'Não concluído' }}</p>
<p><strong>Dificuldade:</strong> {{ $servico->dificuldade }}</p>
<p><strong>Observações:</strong> {{ $servico->obs ?? 'Nenhuma' }}</p>

<a href="{{ route('servico.edit', $servico->id) }}" class="btn btn-primary">Editar</a>

<form action="{{ route('servico.destroy', $servico->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">Excluir</button>
</form>

<a href="{{ route('servico.index') }}" class="btn btn-secondary">Voltar</a>

</x-app-layout>
