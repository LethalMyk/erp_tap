<!-- Botão para exibir a visualização simplificada -->
<button onclick="mostrarConteudo()">Mostrar Visualização</button>

<div id="simplificada" class="view" style="display: none;">
    <h2>Visualização Simplificada</h2>

    <h3>Cliente</h3>
    <p><strong>Nome:</strong> {{ $pedido->cliente->nome }}</p>
    <p><strong>Telefone:</strong> {{ $pedido->cliente->telefone }}</p>

    <h3>Pedido</h3>
    <p><strong>Valor Total:</strong> R$ {{ number_format($pedido->valor, 2, ',', '.') }}</p>

    <!-- Botão de Impressão -->
    <button onclick="imprimir()">Imprimir</button>
</div>

<script>
    // Função para exibir o conteúdo
    function mostrarConteudo() {
        document.getElementById('simplificada').style.display = 'block';
    }

    // Função de impressão
    function imprimir() {
        var conteudo = document.getElementById('simplificada').innerHTML;  // Pega o conteúdo do div
        var janelaImpressao = window.open('', '', 'width=800, height=600'); // Abre uma nova janela
        janelaImpressao.document.write('<html><head><title>Impressão</title></head><body>');
        janelaImpressao.document.write(conteudo); // Escreve o conteúdo na nova janela
        janelaImpressao.document.write('</body></html>');
        janelaImpressao.document.close(); // Fecha o documento para finalizar a construção
        janelaImpressao.print(); // Chama a função de impressão
    }
</script>
