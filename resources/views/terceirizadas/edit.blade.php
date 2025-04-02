<x-app-layout>
    <h1>Editar Serviço Terceirizado</h1>

    <form action="{{ route('terceirizadas.update', $terceirizada->id) }}" method="POST">
        @csrf
        @method('PUT')

        <label>Tipo de Serviço</label>
        <input type="text" name="tipoServico" value="{{ $terceirizada->tipoServico }}" required>

        <label>Observações</label>
        <textarea name="obs">{{ $terceirizada->obs }}</textarea>

        <label>Item Relacionado</label>
        <select name="item_id" required>
            @if($pedido && $pedido->items->count() > 0)
                @foreach($pedido->items as $item)
                    <option value="{{ $item->id }}" 
                        @if($terceirizada->item_id == $item->id) selected @endif>
                        {{ $item->descricao }}
                    </option>
                @endforeach
            @else
                <option disabled>Nenhum item disponível</option>
            @endif
        </select>

        <button type="submit">Atualizar</button>
    </form>
</x-app-layout>
