<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalhes do Pedido') }}
        </h2>
    </x-slot>

    <div class="container mx-auto p-6">
        <!-- Cliente e Pedido lado a lado -->
        <div id="cliente-pedido" class="mt-6 bg-white p-4 rounded-lg shadow flex flex-wrap">
            <div class="w-full">
                <p><strong>Nome:</strong> {{ $pedido->client->nome ?? 'Não disponível' }}</p>
                <p><strong>Endereço:</strong> {{ $pedido->client->endereco ?? 'Não disponível' }}</p>
                <p><strong>Data Pedido:</strong> {{ $pedido->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="w-full">
                <p><strong>Retirada:</strong> {{ $pedido->data_retirada }}</p>
                <p><strong>Observações:</strong> {{ $pedido->obs ?? 'Sem observações' }}</p>
                <p><strong>Telefone:</strong> {{ $pedido->client->telefone ?? 'Não disponível' }}</p>
                <p>{{ $pedido->client->email ?? 'Não disponível' }}</p>
            </div>
            <div class="w-full">
                <p><strong>Prazo:</strong> {{ $pedido->prazo }}</p>
                <p><strong>PG:</strong> {{ $pedido->status }}</p>
                <p><strong>CPF:</strong> {{ $pedido->client->cpf ?? 'Não disponível' }}</p>
            </div>
            <div class="w-1/2">
                <p><strong>Pedido N°</strong>#{{ $pedido->pedido_id }}</p>
            </div>
        </div>

        <!-- Itens do Pedido -->
        <div id="itens-pedido" class="mt-6 bg-white p-4 rounded-lg shadow">
            <h3 class="font-bold text-lg mb-2">Itens do Pedido ({{ $pedido->itens->count() }})</h3>
            <div class="flex flex-wrap gap-6">
                @foreach ($pedido->itens as $item)
                    <div class="bg-gray-100 p-4 rounded-lg shadow">
                        <p><strong>{{ $item->nome_item }}</strong>          Tecido:<strong>{{ $item->material }}</strong>    Qnt:<strong>{{ $item->metragem }}m</strong></p>
                        <p>{!! nl2br(e($item->especificacao)) !!}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Imagens -->
        <div id="imagens-pedido" class="mt-6 bg-white p-4 rounded-lg shadow">
            <h3 class="font-bold text-lg mb-2">Imagens do Pedido</h3>
            <div class="flex flex-wrap gap-2">
                @foreach($pedido->imagens as $imagem)
                    <img src="{{ asset('storage/'.$imagem->imagem_path) }}" alt="Imagem do Pedido" class="w-20 h-20 object-cover rounded-lg shadow">
                @endforeach
            </div>
        </div>

        <!-- Valor Total -->
        <div id="valor-total" class="mt-6 bg-white p-4 rounded-lg shadow">
            <h3 class="font-bold text-lg mb-2">Valor Total</h3>
            <p><strong>Valor Total:</strong> R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</p>
            <p><strong>Status do pagamento:</strong> {{ $pedido->status }}</p>
        </div>

        <!-- Pagamentos -->
        <div id="pagamentos-pedido" class="mt-6 bg-white p-4 rounded-lg shadow">
            <h3 class="font-bold text-lg mb-2">Pagamentos Realizados</h3>
            <div class="flex flex-wrap gap-6">
                @foreach ($pedido->pagamentos as $pagamento)
                    <div class="bg-gray-100 p-4 rounded-lg shadow">
                        <strong>Pagamento {{ $loop->iteration }}</strong>
                        <p><strong>Valor:</strong> R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</p>
                        <p><strong>Forma de Pagamento:</strong> {{ $pagamento->forma }}</p>
                        <p><strong>Descrição:</strong> {{ $pagamento->descricao }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="container mx-auto p-6">
            <!-- Controle para ajustar o zoom antes de imprimir -->
            <div class="mb-4">
                <label for="zoom-range" class="text-lg">Ajuste a escala de impressão:</label>
                <input type="range" id="zoom-range" min="150" max="300" value="85" step="1" class="w-full mt-2">
                <span id="zoom-value" class="text-lg">150%</span>
            </div>

            <!-- Botões de impressão para seções específicas -->
            <div class="space-x-4">
                <button onclick="imprimirMultipleSections(['cliente-pedido', 'itens-pedido', 'imagens-pedido', 'valor-total', 'pagamentos-pedido'])" class="mb-4 px-4 py-2 bg-blue-500 text-black rounded-lg shadow hover:bg-blue-600">
                    Imprimir Via Completa
                </button>
                <button onclick="imprimirMultipleSections(['cliente-pedido', 'itens-pedido'])" class="mb-4 px-4 py-2 bg-blue-500 text-black rounded-lg shadow hover:bg-blue-600">
                    Imprimir Via Retirada
                </button>
                    <button onclick="imprimirMultipleSections(['cliente-pedido', 'itens-pedido','imagens-pedido'])" class="mb-4 px-4 py-2 bg-blue-500 text-black rounded-lg shadow hover:bg-blue-600">
                    Imprimir Via Tapeçeiro
                </button>
                <button onclick="imprimirSection('valor-total')" class="mb-4 px-4 py-2 bg-blue-500 text-black rounded-lg shadow hover:bg-blue-600">
                    Imprimir Valor Total
                </button>
                <button onclick="imprimirSection('pagamentos-pedido')" class="mb-4 px-4 py-2 bg-blue-500 text-black rounded-lg shadow hover:bg-blue-600">
                    Imprimir Pagamentos Realizados
                </button>
                <button onclick="imprimirMultipleSections(['cliente-pedido', 'itens-pedido', 'imagens-pedido'])" class="mb-4 px-4 py-2 bg-blue-500 text-black rounded-lg shadow hover:bg-blue-600">
                    Imprimir Cliente, Itens e Imagens
                </button>
            </div>
        </div>

        <style>
            @media print {
    /* Esconde o cabeçalho, menus e botões durante a impressão */
    header, nav, button, #zoom-range, #zoom-value, .mb-4 {
        display: none;
    }

    /* Ajusta a escala do conteúdo durante a impressão */
    body {
        transform-origin: top left;
        font-size: 9px;
    }

    /* Ajusta margens e padding */
    .container {
        margin: 0;
        padding: 0;
    }

    .p-4 {
        padding: 0.5rem !important;
    }

    /* Ajusta largura das colunas e imagens */
    .w-1/2, .w-full {
        width: 100% !important;
    }

    .w-20 {
        width: 2rem !important;
    }

    .h-20 {
        height: 2rem !important;
    }

    /* Ajusta itens de pedido para empilhar verticalmente */
    #itens-pedido .flex {
        display: block !important;
    }

    /* Ajuste no espaçamento entre os elementos */
    .gap-6 {
        gap: 1rem !important;
    }

    /* Aplica o zoom */
    body {
        transform: scale(var(--print-scale, 1)); /* Usando uma variável customizada */
    }
}

        </style>

        <script>
            // Função para atualizar a escala conforme o controle
            const zoomRange = document.getElementById('zoom-range');
            const zoomValue = document.getElementById('zoom-value');
            
            // Atualiza a exibição do valor de escala
            zoomRange.addEventListener('input', function() {
                zoomValue.textContent = zoomRange.value + '%';
                document.body.style.setProperty('--print-scale', zoomRange.value / 100);
            });

            // Função de impressão para seções específicas
            function imprimirSection(sectionId) {
                // Esconde todas as seções
                let sections = document.querySelectorAll('.container > div');
                sections.forEach(section => section.style.display = 'none');
                
                // Mostra a seção a ser impressa
                document.getElementById(sectionId).style.display = 'block';
                
                // Executa a impressão
                window.print();

                // Restaura a visibilidade das seções
                sections.forEach(section => section.style.display = 'block');
            }

            // Função de impressão para múltiplas seções
            function imprimirMultipleSections(sectionsIds) {
                // Esconde todas as seções
                let sections = document.querySelectorAll('.container > div');
                sections.forEach(section => section.style.display = 'none');
                
                // Mostra as seções que devem ser impressas
                sectionsIds.forEach(sectionId => {
                    document.getElementById(sectionId).style.display = 'block';
                });
                
                // Executa a impressão
                window.print();

                // Restaura a visibilidade das seções
                sections.forEach(section => section.style.display = 'block');
            }
        </script>
    </div>
</x-app-layout>
