<x-app-layout>
    <div class="container">
        <h1 class="page-title">Lista de Pedidos</h1>
        <br><br><br><br>

     @php
    // Pega os anos distintos dos pedidos (ou pode passar isso do Controller)
    $anos = \App\Models\Pedido::selectRaw('YEAR(created_at) as ano')->distinct()->orderBy('ano', 'desc')->pluck('ano')->toArray();

    // Meses fixos
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
    ];

    // Pega os filtros já selecionados da request para manter o estado
    $anosSelecionados = request('ano', []);
    if (!is_array($anosSelecionados)) $anosSelecionados = [$anosSelecionados];

    $mesesSelecionados = request('mes', []);
    if (!is_array($mesesSelecionados)) $mesesSelecionados = [$mesesSelecionados];
@endphp

  <h2>Filtros</h2>
    <form method="GET" action="{{ route('pedidos.index') }}" style="margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
        <div>
            <label for="id" style="font-weight: 600;">ID:</label><br>
            <input type="number" name="id" id="id" value="{{ request('id') }}" style="padding: 5px; width: 80px;">
        </div>

        <div>
            <label for="nome" style="font-weight: 600;">Nome:</label><br>
            <input type="text" name="nome" id="nome" value="{{ request('nome') }}" style="padding: 5px;">
        </div>

        <div>
            <label for="endereco" style="font-weight: 600;">Endereço:</label><br>
            <input type="text" name="endereco" id="endereco" value="{{ request('endereco') }}" style="padding: 5px;">
        </div>

        <div>
            <label for="telefone" style="font-weight: 600;">Telefone:</label><br>
            <input type="text" name="telefone" id="telefone" value="{{ request('telefone') }}" style="padding: 5px;">
        </div>
                <div>
                    <label for="tapeceiro" style="font-weight: 600;">Tapeceiro:</label><br>
       <select name="tapeceiro" id="tapeceiro" style="padding: 5px;">
           <option value="">Todos</option>
           @foreach(\App\Models\Profissional::orderBy('nome')->get() as $prof)
               <option value="{{ $prof->id }}" {{ request('tapeceiro') == $prof->id ? 'selected' : '' }}>
                   {{ $prof->nome }}
               </option>
           @endforeach
       </select>
   </div>
   
   <div style="margin-top: 10px;">
       <label style="font-weight: 600; cursor: pointer;" onclick="togglePeriodo()" id="togglePeriodoLabel">
        Período <span id="seta" style="display: inline-block; transition: transform 0.3s;">&#x25B6;</span>
    </label>
    
    <div id="periodoContainer" style="display: none; max-height: 220px; overflow-y: auto; border: 1px solid #ccc; padding: 8px; border-radius: 4px; margin-top: 5px;">
        <strong>Ano:</strong><br>
        @foreach($anos as $ano)
            <label style="font-weight: normal; margin-right: 10px;">
                <input type="checkbox" name="ano[]" value="{{ $ano }}" {{ in_array($ano, $anosSelecionados) ? 'checked' : '' }}>
                {{ $ano }}
            </label>
            @endforeach
            
            <br><br>
            <strong>Mês:</strong><br>
        @foreach($meses as $num => $nome)
        <label style="font-weight: normal; margin-right: 10px;">
            <input type="checkbox" name="mes[]" value="{{ $num }}" {{ in_array($num, $mesesSelecionados) ? 'checked' : '' }}>
                {{ $nome }}
            </label>
        @endforeach
    </div>
</div>

<div>
           <label style="font-weight: 600;">Status:</label><br>
           <select name="status" style="padding: 5px;">
               <option value="">Todos</option>
               <option value="RESTA" {{ request('status') == 'RESTA' ? 'selected' : '' }}>RESTA</option>
               <option value="PAGO" {{ request('status') == 'PAGO' ? 'selected' : '' }}>PAGO</option>
           </select>
       </div>

        <div>
            <button type="submit" style="padding: 6px 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Filtrar</button>
        </div>
        <div>
            <a href="{{ route('pedidos.index') }}" style="padding: 6px 12px; background-color: #6c757d; color: white; border-radius: 4px; text-decoration: none;">Limpar</a>
        </div>
    </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Endereço</th>
                        <th>Telefone</th>
                        <th>Data</th>
                        <th>Itens</th>
                        <th>Imagens</th>
                        <th>Data Retirada</th>
                        <th>Andamento</th>
                        <th>Tapeceiro</th>
                        <th>Prazo</th>
                        <th>Data Previsão</th>
                        <th>Status</th>
                        <th>Observação</th>
                        <th>Ações</th> <!-- NOVA COLUNA AÇÕES -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedidos as $pedido)
                        <tr>
                            <td>{{ $pedido->id }}</td>
                            <td>{{ $pedido->cliente ? $pedido->cliente->nome : 'Cliente não encontrado' }}</td>
                            <td>
    {{ $pedido->cliente 
        ? $pedido->cliente->logradouro . ', ' . $pedido->cliente->numero . 
          ($pedido->cliente->complemento ? ' - ' . $pedido->cliente->complemento : '') . 
          ' - ' . $pedido->cliente->bairro . 
          ' - ' . $pedido->cliente->cidade
        : 'Endereço não encontrado' 
    }}
</td>

                            <td>{{ $pedido->cliente ? $pedido->cliente->telefone : 'Telefone não encontrado' }}</td>
                            <td>{{ $pedido->formatarData($pedido->data) ?? '-' }}</td>
                            <td>{{ $pedido->qntItens }}</td>
                            <td>
                                @if($pedido->imagens->count())
                                    <div class="thumbs">
                                        @foreach($pedido->imagens->chunk(2) as $imagemChunk)
                                            <div class="image-pair">
                                                @foreach($imagemChunk as $imagem)
                                                    <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do pedido" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Sem imagens</span>
                                @endif
                            </td>
<td>
    @if($pedido->data_retirada)
        {{ $pedido->formatarData($pedido->data_retirada) }}
    @else
        <span class="text-gray-400 italic">Não registrada</span>
    @endif
</td>                            <td>{{ $pedido->andamento }}</td>
                            <td>{{ $pedido->profissional ? $pedido->profissional->nome : 'Distribuir' }}</td>
                            <td>{{ $pedido->formatarData($pedido->prazo) ?? '-' }}</td>
<td>
    @if($pedido->data_previsao)
        {{ $pedido->formatarData($pedido->data_previsao) }}
    @else
        <span class="text-gray-400 italic">Sem previsão</span>
    @endif
</td>                            <td>{{ $pedido->status }}</td>
                            <td>{{ $pedido->obs ?? '-' }}</td> 
                            <td>
                                <a href="{{ route('pedido.visualizar', $pedido->id) }}" class="btn-view">Ver</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        select.filter-input {
            font-size: 12px;
            min-width: 100px;
            min-height: 50px;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .thumbs {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            max-width: 200px;
            overflow-x: auto;
        }

        .thumbs .image-pair {
            display: flex;
            gap: 6px;
        }

        .thumbs img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .btn-create {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .btn-create:hover {
            background-color: #218838;
        }

        .filters {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .btn-filter {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-filter:hover {
            background-color: #0056b3;
        }

        .btn-clear {
            display: inline-block;
            margin-left: 10px;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .btn-clear:hover {
            background-color: #5a6268;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        thead {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td {
            font-size: 1rem;
            color: #555;
        }

        /* Estilo botão Ver */
        .btn-view {
            background-color: #17a2b8;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }
        .btn-view:hover {
            background-color: #117a8b;
        }

        /* Linha selecionada */
tr.selected {
    background-color: #ffeeba !important; /* amarelo claro */
}

    </style>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('table tbody tr');

    rows.forEach(row => {
        row.addEventListener('click', function() {
            // Remove a seleção de todas as linhas
            rows.forEach(r => r.classList.remove('selected'));
            // Adiciona a classe 'selected' à linha clicada
            this.classList.add('selected');
        });

        // Duplo clique para ir ao pedido
        row.addEventListener('dblclick', function() {
            const pedidoId = this.querySelector('td').innerText;
            window.location.href = `/pedido/${pedidoId}/visualizar`;
        });
    });
});
</script>

    <script>
function togglePeriodo() {
    const container = document.getElementById('periodoContainer');
    const seta = document.getElementById('seta');

    if (container.style.display === 'none' || container.style.display === '') {
        container.style.display = 'block';
        seta.style.transform = 'rotate(90deg)';
    } else {
        container.style.display = 'none';
        seta.style.transform = 'rotate(0deg)';
    }
}
</script>
<script>
    // Espera o DOM carregar
    document.addEventListener('DOMContentLoaded', function() {
        // Seleciona todas as linhas do tbody
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            row.addEventListener('dblclick', function() {
                // Pega o ID do pedido da primeira célula da linha
                const pedidoId = this.querySelector('td').innerText;
                // Redireciona para a página de visualização do pedido
                window.location.href = `/pedido/${pedidoId}/visualizar`;
            });
        });
    });
</script>

</x-app-layout>
