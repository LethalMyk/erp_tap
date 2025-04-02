<x-app-layout>
    <h1>Detalhes do Serviço Terceirizado</h1>

    <p><strong>ID:</strong> {{ $terceirizada->id }}</p>
    <p><strong>Tipo de Serviço:</strong> {{ $terceirizada->tipoServico }}</p>
    <p><strong>Observações:</strong> {{ $terceirizada->obs ?? 'Nenhuma' }}</p>
<p><strong>Item Relacionado:</strong> {{ $terceirizada->item->nomeItem ?? 'Sem item associado' }}</p>
    <p><strong>Data de Criação:</strong> {{ $terceirizada->created_at->format('d/m/Y H:i') }}</p>
    <p><strong>Última Atualização:</strong> {{ $terceirizada->updated_at->format('d/m/Y H:i') }}</p>

    <a href="{{ route('terceirizadas.edit', $terceirizada->id) }}">Editar</a>
    <form action="{{ route('terceirizadas.destroy', $terceirizada->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Deseja excluir este serviço?')">Excluir</button>
    </form>
    <a href="{{ route('terceirizadas.index') }}">Voltar</a>
</x-app-layout>
