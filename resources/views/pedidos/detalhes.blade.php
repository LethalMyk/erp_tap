<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes do Pedido') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <!-- Detalhes do Cliente -->
        <div class="cliente-dados">
            <h3>Dados do Cliente</h3>
            <p><strong>Nome:</strong> {{ $pedido->client->nome ?? 'Não disponível' }}</p>
            <p><strong>Telefone:</strong> {{ $pedido->client->telefone ?? 'Não disponível' }}</p>
            <p><strong>Endereço:</strong> {{ $pedido->client->endereco ?? 'Não disponível' }}</p>
            <p><strong>Email:</strong> {{ $pedido->client->email ?? 'Não disponível' }}</p>
            <p><strong>CPF:</strong> {{ $pedido->client->cpf ?? 'Não disponível' }}</p>
        </div>

        <!-- Detalhes do Pedido -->
        <div class="dados-pedido">
            <h3>Dados do Pedido</h3>
            <p><strong>ID do Pedido:</strong> {{ $pedido->pedido_id }}</p>
            <p><strong>Status:</strong> {{ $pedido->status }}</p>
            <p><strong>Data do Pedido:</strong> {{ $pedido->created_at->format('d/m/Y') }}</p>
            <p><strong>Data de Retirada:</strong> {{ $pedido->data_retirada }}</p>
            <p><strong>Observações:</strong> {{ $pedido->obs ?? 'Sem observações' }}</p>
            <p><strong>Prazo:</strong> {{ $pedido->prazo }}</p>
        </div>

        <!-- Itens do Pedido -->
        <div id="items" class="itens">
            <h3>Itens do Pedido</h3>
            @foreach ($pedido->itens as $item)
                <div class="item">
                    <p><strong>Nome do Item:</strong> {{ $item->nome_item }}</p>
                    <p><strong>Material:</strong> {{ $item->material }}</p>
                    <p><strong>Metragem:</strong> {{ $item->metragem }} metros</p>
                    <p><strong>Especificação:</strong> {{ $item->especificacao }}</p>
                </div>
                <hr>
            @endforeach
        </div>

        <!-- Pagamentos -->
        <div id="pagamentos">
            <h3>Pagamentos Realizados</h3>
            @foreach ($pedido->pagamentos as $pagamento)
                <div class="campo pagamento">
                    <strong>Pagamento {{ $loop->iteration }}</strong>
                    <p><strong>Valor:</strong> R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</p>
                    <p><strong>Forma de Pagamento:</strong> {{ $pagamento->forma }}</p>
                    <p><strong>Descrição:</strong> {{ $pagamento->descricao }}</p>
                </div>
                <hr>
            @endforeach
        </div>

        <!-- Imagens -->
        <div class="campo">
            <h3>Imagens do Pedido</h3>
@foreach($pedido->imagens as $imagem)
    <img src="{{ asset('storage/'.$imagem->imagem_path) }}" alt="Imagem do Pedido" class="w-32 h-32 object-cover">
@endforeach
        </div>
    
        <!-- Valor Total -->
        <div class="campo">
            <h3>Valor Total</h3>
            <p><strong>Valor Total:</strong> R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</p>
        </div>
    </div>
</x-app-layout>
