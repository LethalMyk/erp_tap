<style>
/* Container da via tap sem margin/padding */
.via-tap {
    font-family: Arial, sans-serif;
    font-size: 10px;
    color: #000;
    padding: 0 !important;
    margin: 0 !important;
    width: 100%;
}

/* Remover margin/padding extras da aba */
#viatap {
    padding: 0 !important;
    margin: 0 !important;
}

/* Pedido # maior */
.via-tap h2 {
    font-size: 18px;
    margin: 5px 0;
    text-align: center;
}

/* Títulos menores do resto */
.via-tap h1, 
.via-tap h3 {
    font-size: 12px;
    margin: 3px 0;
    padding: 0;
    text-align: center;
}

/* Texto da tabela */
.via-tap table {
    width: 100%;
    border-collapse: collapse;
    font-size: 10px;
    margin: 0;
    padding: 0;
}

.via-tap td, 
.via-tap th {
    padding: 2px 4px;
    vertical-align: top;
}

/* Alinhar conteúdo da célula direita */
.via-tap td.right {
    text-align: right;
}

/* Campos principais maiores */
.via-tap .destaque {
    font-size: 13px;
    font-weight: bold;
}

/* Seções */
.via-tap .section {
    border-top: 1px solid #ccc;
    margin-top: 5px;
    padding-top: 5px;
    page-break-inside: avoid;
}

/* Container de imagens */
.via-tap .imagens-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 6px;
    margin-top: 5px;
    page-break-inside: avoid;
}

.via-tap .imagens-container img {
    display: block;
    height: auto;
    page-break-inside: avoid;
}

/* Fonte adaptável por quantidade de itens */
.itens-1 p,
.itens-2 p,
.itens-3 p,
.itens-4 p,
.itens-5 p {
    font-size: 10px;
}

.itens-6 p,
.itens-7 p,
.itens-8 p {
    font-size: 9.5px;
}

.itens-9 p,
.itens-10 p,
.itens-11 p,
.itens-12 p,
.itens-13 p {
    font-size: 9px;
    margin-bottom: 1px;
}

.itens-14 p,
.itens-15 p {
    font-size: 8.5px;
    margin-bottom: 1px;
}

/* Linha separadora */
.via-tap hr {
    margin: 3px 0;
}

/* Tamanho adaptável das imagens */
.imagens-1 img,
.imagens-2 img {
    max-width: 40%;
}

.imagens-3 img,
.imagens-4 img {
    max-width: 28%;
}

.imagens-5 img,
.imagens-6 img,
.imagens-7 img,
.imagens-8 img {
    max-width: 18%;
}

.imagens-9 img,
.imagens-10 img,
.imagens-11 img,
.imagens-12 img {
    max-width: 14%;
}

/* Estilo especial para o prazo de entrega */
.via-tap .prazo-entrega {
    font-size: 20px;
    font-weight: bold;
    color: #000;
    text-align: center;
    margin: 10px 0;
}

/* Botão imprimir */
.via-tap .btn-primary {
    margin: 5px 0 0 0 !important;
    font-size: 10px;
    padding: 5px 10px;
}
</style>

<div class="via-tap" id="viatap">
    <h2>Pedido #{{ $pedido->id }}</h2>

    <table>
        <tr>
            <td>
                <p class="destaque"><strong>Nome:</strong> {{ $pedido->cliente->nome }}</p>
                <p class="destaque"><strong>Endereço:</strong> {{ $pedido->cliente->endereco }}</p>
                <p class="destaque"><strong>Telefone:</strong> {{ $pedido->cliente->telefone }}</p>
            </td>
            <td class="right">
                <p class="destaque"><strong>Orçamento dia:</strong> {{ $pedido->data }}</p>
                <p class="destaque"><strong>Itens Retirados dia:</strong> {{ $pedido->dataRetirada }}</p>
            </td>
        </tr>
    </table>

    @php
        $qtdImagens = count($pedido->imagens);
        $qtdItens = count($pedido->items);
    @endphp

    <div class="section itens-{{ $qtdItens }}">
        @foreach($pedido->items as $item)
            <p><strong>{{ $item->nomeItem }}</strong> |
               <strong>Material:</strong> {{ $item->material }} |
               <strong>Metragem:</strong> {{ $item->metragem }} m
            </p>
            <p>{{ $item->especifi }}</p>
            <hr>
        @endforeach
    </div>

    @if($qtdImagens > 0)
    <div class="imagens-container imagens-{{ $qtdImagens }}">
        @foreach($pedido->imagens as $imagem)
            <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do pedido">
        @endforeach
    </div>
    @endif

    <div class="section">
        <p class="prazo-entrega"><strong>Prazo de Entrega:</strong> {{ $pedido->prazo }}</p>
    </div>

    <a href="{{ route('pedidos.imprimirviatap', $pedido->id) }}" target="_blank" class="btn btn-primary">
        🖨️ Imprimir Via
    </a>
</div>
