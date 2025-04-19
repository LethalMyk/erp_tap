<x-app-layout>
    <div class="container">
        <h1 class="page-title">Lista de Pedidos</h1>
        <a href="{{ route('formulario.index') }}" class="btn-create">Criar Pedido</a>
        <br><br>        <br><br>


        <!-- Formulário de Filtros -->
        <form action="{{ route('pedidos.index') }}" method="GET" class="mb-4">
            <button type="submit" class="btn-filter">Filtrar</button>
            
                 <a href="{{ route('pedidos.index') }}" class="btn-clear">Limpar Filtros</a>
            <div class="filters">
                <input type="text" name="id" value="{{ $id }}" placeholder="Filtrar por ID" class="filter-input">
                <input type="text" name="cliente_nome" value="{{ $clienteNome }}" placeholder="Filtrar por Nome do Cliente" class="filter-input">
                <input type="text" name="endereco" value="{{ $endereco }}" placeholder="Filtrar por Endereço" class="filter-input">
                <input type="text" name="telefone" value="{{ $telefone }}" placeholder="Filtrar por Telefone" class="filter-input">
                <input type="date" name="data" value="{{ $data }}" class="filter-input">
                <input type="text" name="andamento" value="{{ $andamento }}" placeholder="Filtrar por Andamento" class="filter-input">
                <input type="text" name="tapeceiro" value="{{ $tapeceiro }}" placeholder="Filtrar por Tapeceiro" class="filter-input">
            </div>
        </form>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Endereço</th>
                        <th>Telefone</th>
                        <th>Data</th>
                        <th>Quantidade de Itens</th>
                        <th>Fotos</th>
                        <th>Andamento</th>
                        <th>Tapeceiro</th>
                        <th>StatusPG</th>
                        <th>Ações</th>
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
                                                    <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do pedido" style="width: 100px; height: auto;">
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 italic">Sem imagens</span>
                                @endif
                            </td>
                            <td>{{ $pedido->andamento }}</td>
                            <td>
                                @if($pedido->servicos->count())
                                    @foreach($pedido->servicos as $servico)
                                        {{ $servico->profissional ? $servico->profissional->nome : 'Profissional não encontrado' }}
                                    @endforeach
                                @else
                                    'Profissional não encontrado'
                                @endif
                            </td>
                            <td>{{ $pedido->status }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('pedido.visualizar', $pedido->id) }}" class="btn-view">Ver</a>
                                    <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn-edit">Editar</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
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
            max-width: 1200px;
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
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .filter-group input, .filter-group select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 200px;
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

        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 20px;
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

        a {
            text-decoration: none;
            font-weight: bold;
        }

        .btn-view {
            color: #007bff;
            padding: 5px 10px;
            margin-right: 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-view:hover {
            background-color: #007bff;
            color: white;
        }

        .btn-edit {
            color: #ffc107;
            padding: 5px 10px;
            border: 1px solid #ffc107;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        .btn-edit:hover {
            background-color: #ffc107;
            color: black;
        }

        td.empty {
            color: #aaa;
            font-style: italic;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }
    </style>
</x-app-layout>
