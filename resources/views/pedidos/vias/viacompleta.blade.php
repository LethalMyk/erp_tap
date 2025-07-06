    <div class="container">

        <h2>Visualização do Pedido</h2>

        <h3>Cliente</h3>
        <ul>
            <li><strong>Nome:</strong> {{ $pedido->cliente->nome }}</li>
            <li><strong>Telefone:</strong> {{ $pedido->cliente->telefone }}</li>
            <li><strong>Endereço:</strong> {{ $pedido->cliente->endereco }}</li>
            <li><strong>CPF:</strong> {{ $pedido->cliente->cpf }}</li>
            <li><strong>Email:</strong> {{ $pedido->cliente->email }}</li>
        </ul>

        <h3>Pedido</h3>
        <ul>
            <li><strong>Data:</strong> {{ $pedido->data }}</li>
            <li><strong>Data de Retirada:</strong> {{ $pedido->data_retirada }}</li>
            <li><strong>Prazo:</strong> {{ $pedido->prazo }}</li>
            <li><strong>Valor Total:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</li>
            <li><strong>Valor Restante:</strong> R$ {{ number_format($pedido->valor_resta, 2, ',', '.') }}</li>
        </ul>

        <h3>Itens</h3>
        @foreach($pedido->items as $item)
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
                <p><strong>Nome do Item:</strong> {{ $item->nomeItem }}</p>
                <p><strong>Material:</strong> {{ $item->material }}</p>
                <p><strong>Metragem:</strong> {{ $item->metragem }} m</p>
                <p><strong>Especificações:</strong> {{ $item->especifi }}</p>

                <h4>Serviços Terceirizados</h4>
                @forelse($item->terceirizadas as $terc)
                    <p>- {{ $terc->tipoServico }} (Obs: {{ $terc->obs }})</p>
                @empty
                    <p>Sem serviços terceirizados</p>
                @endforelse
            </div>
        @endforeach

        <div style="border: 2px solid #000; padding: 15px; margin: 20px 0; border-radius: 8px;">
            <h3 style="color:rgb(22, 22, 22);">Valor do Pedido</h3>
            <p><strong>Total:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</p>
            <p><strong>Valor Restante:</strong> R$ {{ number_format($pedido->valor_resta, 2, ',', '.') }}</p>
        </div>

 

        @foreach ($pedido->pagamentos as $pagamento)
    <div class="pagamento">
        <p><strong>Data:</strong> {{ $pagamento->data }}</p>
        <p><strong>Valor:</strong> R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</p>
        <p><strong>Forma:</strong> {{ $pagamento->forma }}</p>


        @if ($pagamento->status === 'EM ABERTO')
    <form action="{{ route('pagamento.registrar', $pagamento->id) }}" method="POST" style="margin-top:10px;">
        @csrf
        <input type="text" name="obs" placeholder="Observação do registro (opcional)" style="width: 70%; padding: 5px;" />
        <button type="submit" onclick="return confirm('Confirmar registro do pagamento?')">
            ✅ Registrar Pagamento
        </button>
    </form>
@endif

    </div>
            <p><strong>Status:</strong> {{ $pagamento->status }}</p>

            @if ($pagamento->data_registro)
            <p><strong>Registrado em:</strong> {{ \Carbon\Carbon::parse($pagamento->data_registro)->format('d/m/Y') }}</p>
            @endif
            <hr>
            @endforeach


        <h3>Imagens</h3>
        <div style="display: flex; flex-wrap: wrap;">
            @foreach($pedido->imagens as $imagem)
                <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do pedido" style="max-width: 200px; margin: 10px;">
            @endforeach
        </div>

<a href="{{ route('pedidos.imprimirviacompleta', $pedido->id) }}" target="_blank" class="btn btn-primary" style="margin-bottom: 20px;">
🖨️ Imprimir Via
</a>

    </div>
