<x-app-layout>
    <div class="container">
        <style>
            .view-btn {
                margin-right: 10px;
            }

            .view-btn.active {
                background-color: #0d6efd !important;
                color: #fff !important;
                border-color: #0d6efd !important;
            }
        </style>

        <div style="margin-bottom: 20px;">
            <button onclick="setView('completa')" class="btn btn-secondary view-btn" id="btn-completa">Via Completa</button>
            <button onclick="setView('simplificada')" class="btn btn-secondary view-btn" id="btn-simplificada">Via Simplificada</button>
            <button onclick="setView('retirada')" class="btn btn-secondary view-btn" id="btn-retirada">Via Retirada</button>
        </div>

        <script>
            function setView(viewType) {
                // Esconde todas as visualizações
                document.querySelectorAll('.view').forEach(v => v.style.display = 'none');
                document.getElementById(viewType).style.display = 'block';

                // Remove classe "active" de todos os botões
                document.querySelectorAll('.view-btn').forEach(btn => btn.classList.remove('active'));

                // Adiciona classe "active" ao botão correspondente
                document.getElementById('btn-' + viewType).classList.add('active');

                // Exibe o botão de impressão conforme a visualização
                if (viewType === 'simplificada') {
                    document.getElementById('btn-imprimir-simplificada').style.display = 'inline-block';
                    document.getElementById('btn-imprimir-retirada').style.display = 'none';
                } else if (viewType === 'retirada') {
                    document.getElementById('btn-imprimir-retirada').style.display = 'inline-block';
                    document.getElementById('btn-imprimir-simplificada').style.display = 'none';
                } else {
                    document.getElementById('btn-imprimir-simplificada').style.display = 'none';
                    document.getElementById('btn-imprimir-retirada').style.display = 'none';
                }
            }

            window.onload = function () {
                setView('completa'); // Visual padrão
            };
        </script>
        <hr>

        <!-- Via Completa -->
        <div id="completa" class="view">
            <!-- TODO: tudo que você já tem atualmente vai aqui -->
            <!-- Cliente, Pedido, Itens, Pagamentos, Imagens -->
            
            <h2>Visualização do Pedido</h2>
            <h3>Cliente</h3>
            <ul>
                <li><strong>Nome:</strong>{{ $pedido->cliente->nome }}</li>
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
            
            <div style="border: 2px ; padding: 15px; margin: 20px 0; border-radius: 8px;">
                <h3 style="color:rgb(22, 22, 22);">Valor do Pedido</h3>
                <p><strong>Total:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</p>
                <p><strong>Valor Restante:</strong> R$ {{ number_format($pedido->valorResta, 2, ',', '.') }}</p>
            </div>
            
            <h3>Pagamentos Realizados</h3>
            @foreach($pedido->pagamentos as $pagamento)
                <div>
                    <p><strong>Valor:</strong> R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</p>
                    <p><strong>Forma:</strong> {{ $pagamento->forma }}</p>
                    <p><strong>Obs:</strong> {{ $pagamento->obs }}</p>
                </div>
            @endforeach
            
            <h3>Imagens</h3>
            <div style="display: flex; flex-wrap: wrap;">
                @foreach($pedido->imagens as $imagem)
                    <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do pedido" style="max-width: 200px; margin: 10px;">
                @endforeach
            </div>
            <a href="{{ route('pedidos.imprimirviatap', $pedido->id) }}" target="_blank" class="btn btn-primary" style="margin-bottom: 20px;">
                🖨️ Imprimir Via
            </a>
        </div>

        <!-- Via Simplificada -->
        <div id="simplificada" class="view" style="display: none;">
            <h3>Cliente</h3>
            <p><strong>Nome:</strong> {{ $pedido->cliente->nome }}</p>
            <p><strong>Telefone:</strong> {{ $pedido->cliente->telefone }}</p>

            <h3>Pedido</h3>
            <p><strong>Valor Total:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</p>

            <!-- Botão de Impressão para Simplificada -->
            <button id="btn-imprimir-simplificada" onclick="imprimir('simplificada')" style="display:none; margin-top: 20px;">Imprimir Via Simplificada</button>
        </div>

        <!-- Via Retirada -->
        <div id="retirada" class="view" style="display: none;">
            <h3>Retirada</h3>
            <p><strong>Data de Retirada:</strong> {{ $pedido->data_retirada }}</p>
            <p><strong>Nome do Cliente:</strong> {{ $pedido->cliente->nome }}</p>

            <!-- Botão de Impressão para Retirada -->
            <button id="btn-imprimir-retirada" onclick="imprimir('retirada')" style="display:none; margin-top: 20px;">Imprimir Via Retirada</button>
        </div>

    </div>

    <script>
        function imprimir(viewType) {
            var conteudo = document.getElementById(viewType).innerHTML;
            var janelaImpressao = window.open('', '', 'width=800, height=600');
            janelaImpressao.document.write('<html><head><title>Impressão</title></head><body>');
            janelaImpressao.document.write(conteudo);
            janelaImpressao.document.write('</body></html>');
            janelaImpressao.document.close();
            janelaImpressao.print();
        }
    </script>
</x-app-layout>
