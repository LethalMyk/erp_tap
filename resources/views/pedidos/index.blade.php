<x-app-layout>
    <div class="container">
        <h1 class="page-title">Lista de Pedidos</h1>
        <a href="{{ route('formulario.index') }}" class="btn-create">Criar Pedido</a>
        <br><br><br><br>

        <!-- Formulário de Filtros e Ordenação -->
        <form action="{{ route('pedidos.index') }}" method="GET" class="mb-4">
            <div class="filters">
                <input type="text" name="id" value="{{ request('id') }}" placeholder="Filtrar por ID" class="filter-input">
                <input type="text" name="cliente_nome" value="{{ request('cliente_nome') }}" placeholder="Filtrar por Nome do Cliente" class="filter-input">
                <input type="text" name="endereco" value="{{ request('endereco') }}" placeholder="Filtrar por Endereço" class="filter-input">
                <input type="text" name="telefone" value="{{ request('telefone') }}" placeholder="Filtrar por Telefone" class="filter-input">
                <input type="date" name="data" value="{{ request('data') }}" class="filter-input">

                <select name="andamento[]" multiple class="filter-input" style="height: auto;">
                    <option value="Produzindo" {{ in_array('Produzindo', (array) request('andamento', [])) ? 'selected' : '' }}>Produzindo</option>
                    <option value="Retirar" {{ in_array('Retirar', (array) request('andamento', [])) ? 'selected' : '' }}>Retirar</option>
                    <option value="Resta" {{ in_array('Resta', (array) request('andamento', [])) ? 'selected' : '' }}>Resta</option>
                    <option value="Entregue" {{ in_array('Entregue', (array) request('andamento', [])) ? 'selected' : '' }}>Entregue</option>
                </select>

                <input type="text" name="tapeceiro" value="{{ request('tapeceiro') }}" placeholder="Filtrar por Tapeceiro" class="filter-input">

                <label for="mes">Pronto no mês de</label>
                <select name="mes" id="mes" class="filter-input">
                    <option value="">Selecione o Mês</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('mes') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>

                <!-- Campo para ordenar tapeceiros em ordem customizada -->
                <input
                    type="text"
                    name="custom_order"
                    value="{{ request('custom_order') }}"
                    placeholder="Ordenar Tapeceiro (ex: Samuel,Paulo,João)"
                    class="filter-input"
                />

                <!-- Select para ordenação tradicional -->
                <select name="sort_field" class="filter-input">
                    <option value="">Ordenar por</option>
                    <option value="id" {{ request('sort_field') == 'id' ? 'selected' : '' }}>ID</option>
                    <option value="data" {{ request('sort_field') == 'data' ? 'selected' : '' }}>Data</option>
                    <option value="cliente_nome" {{ request('sort_field') == 'cliente_nome' ? 'selected' : '' }}>Nome do Cliente</option>
                    <option value="andamento" {{ request('sort_field') == 'andamento' ? 'selected' : '' }}>Andamento</option>
                    <option value="tapeceiro" {{ request('sort_field') == 'tapeceiro' ? 'selected' : '' }}>Tapeceiro</option>
                    <option value="prazo" {{ request('sort_field') == 'prazo' ? 'selected' : '' }}>Prazo</option>
                </select>

                <select name="sort_direction" class="filter-input">
                    <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                    <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                </select>
            </div>

            <button type="submit" class="btn-filter">Filtrar</button>
            <a href="{{ route('pedidos.index') }}" class="btn-clear">Limpar Filtros</a>
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
                        <th>Data Início</th>
                        <th>Data Término</th>
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
                            <td>{{ $pedido->cliente ? $pedido->cliente->endereco : 'Endereço não encontrado' }}</td>
                            <td>{{ $pedido->cliente ? $pedido->cliente->telefone : 'Telefone não encontrado' }}</td>
                            <td>{{ $pedido->data }}</td>
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
                            <td>{{ $pedido->data_retirada ?? 'Não registrada' }}</td>
                            <td>{{ $pedido->andamento }}</td>
                            <td>{{ $pedido->tapeceiro ?? 'Tapeceiro não informado' }}</td>
                            <td>{{ $pedido->prazo ?? 'Não informado' }}</td>
                            <td>{{ $pedido->data_inicio ?? 'Não informado' }}</td>
                            <td>{{ $pedido->data_termino ?? 'Não informado' }}</td>
                            <td>{{ $pedido->data_previsao ?? 'Sem previsão' }}</td>
                            <td>{{ $pedido->status }}</td>
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
    </style>
</x-app-layout>
