<x-app-layout>
    <div class="container" x-data="{ openModal: null }">
        <h1 class="page-title">Lista de Pedidos</h1>
        <a href="{{ route('formulario.index') }}" class="btn-create">Criar Pedido</a>
        <br><br><br><br>

        <!-- Formulário de Filtros (unificado) -->
        <form action="{{ route('producao.index') }}" method="GET" class="mb-4">
            <button type="submit" class="btn-filter">Filtrar</button>
            <a href="{{ route('producao.index') }}" class="btn-clear">Limpar Filtros</a>
            
            <div class="filters">
                <input type="text" name="id" value="{{ $id }}" placeholder="Filtrar por ID" class="filter-input">
                <input type="text" name="cliente_nome" value="{{ $clienteNome }}" placeholder="Filtrar por Nome do Cliente" class="filter-input">
                <input type="text" name="endereco" value="{{ $endereco }}" placeholder="Filtrar por Endereço" class="filter-input">
                <input type="text" name="telefone" value="{{ $telefone }}" placeholder="Filtrar por Telefone" class="filter-input">
                <input type="date" name="data" value="{{ $data }}" class="filter-input">

                <select name="andamento[]" multiple class="filter-input" style="height: auto;">
                    <option value="Produzindo" {{ in_array('Produzindo', (array) $andamento ?? []) ? 'selected' : '' }}>Produzindo</option>
                    <option value="Retirar" {{ in_array('Retirar', (array) $andamento ?? []) ? 'selected' : '' }}>Retirar</option>
                    <option value="Resta" {{ in_array('Resta', (array) $andamento ?? []) ? 'selected' : '' }}>Resta</option>
                    <option value="Entregue" {{ in_array('Entregue', (array) $andamento ?? []) ? 'selected' : '' }}>Entregue</option>
                </select>

                <input type="text" name="tapeceiro" value="{{ $tapeceiro }}" placeholder="Filtrar por Tapeceiro" class="filter-input">
                
                <!-- Filtro de mês -->
                <label for="mes">Pronto no mês de</label>
                <select name="mes" id="mes" class="filter-input">
                    <option value="">Selecione o Mês</option>
                    <option value="1" {{ request('mes') == 1 ? 'selected' : '' }}>Janeiro</option>
                    <option value="2" {{ request('mes') == 2 ? 'selected' : '' }}>Fevereiro</option>
                    <option value="3" {{ request('mes') == 3 ? 'selected' : '' }}>Março</option>
                    <option value="4" {{ request('mes') == 4 ? 'selected' : '' }}>Abril</option>
                    <option value="5" {{ request('mes') == 5 ? 'selected' : '' }}>Maio</option>
                    <option value="6" {{ request('mes') == 6 ? 'selected' : '' }}>Junho</option>
                    <option value="7" {{ request('mes') == 7 ? 'selected' : '' }}>Julho</option>
                    <option value="8" {{ request('mes') == 8 ? 'selected' : '' }}>Agosto</option>
                    <option value="9" {{ request('mes') == 9 ? 'selected' : '' }}>Setembro</option>
                    <option value="10" {{ request('mes') == 10 ? 'selected' : '' }}>Outubro</option>
                    <option value="11" {{ request('mes') == 11 ? 'selected' : '' }}>Novembro</option>
                    <option value="12" {{ request('mes') == 12 ? 'selected' : '' }}>Dezembro</option>
                </select>
            </div>
        </form>

        <div class="table-container">
            <table>
<thead>
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Endereço</th>
        <th>Items</th>
        <th>Imagens</th>
        <th>Data Retirada</th>
        <th>Andamento</th>
        <th>Tapeceiro</th>
        <th>Prazo</th>
        <th>Data Início</th>
        <th>Data Término</th>
        <th>Terceirizadas</th>
        <th>Previsão Entrega</th>
                <th>Observação</th>

        <th>Ações</th>
    </tr>
</thead>
<tbody>
    @foreach($pedidos as $pedido)
        <tr>
            <td>{{ $pedido->id }}</td>
            <td>{{ $pedido->cliente->nome ?? 'Cliente não encontrado' }}</td>
            <td>{{ $pedido->cliente->endereco ?? 'Endereço não encontrado' }}</td>
<td>
    @if($pedido->items->count())
        {{ $pedido->items->pluck('nomeItem')->filter()->implode(', ') }}
    @else
        -
    @endif
</td>
            <td>
                @if($pedido->imagens->count())
                    <div class="thumbs">
                        @foreach($pedido->imagens->take(4) as $imagem)
                            <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem" />
                        @endforeach
                    </div>
                @else
                    <span class="text-gray-400 italic">Sem imagens</span>
                @endif
            </td>
            <td>{{ $pedido->data_retirada ? \Carbon\Carbon::parse($pedido->data_retirada)->format('d/m/Y') : '---' }}</td>
            <td>{{ $pedido->andamento }}</td>
            <td>{{ $pedido->tapeceiro ?? 'Tapeceiro não registrado' }}</td>
            <td>{{ $pedido->prazo ?? '---' }}</td>
            <td>{{ $pedido->data_inicio ?? '---' }}</td>
            <td>{{ $pedido->data_termino ?? '---' }}</td>
            <td>
    @if($pedido->terceirizadas->count())
        {{ $pedido->terceirizadas->pluck('tipoServico')->implode(', ') }}
    @else
        -
    @endif
</td>

            <td>{{ $pedido->data_previsao ?? '---' }}</td>
                         <td>{{ $pedido->obs ?? '-' }}</td> 

            <td>
                <div class="action-buttons">
                    <a href="{{ route('pedido.visualizar', $pedido->id) }}" class="btn-view">Ver</a>
                    <button @click="openModal = {{ $pedido->id }}" class="btn-edit">Editar Rápido</button>
<a 
    href="{{ route('agendamentos.calendario', [
        'cliente_id' => $pedido->cliente->id ?? null,
        'data' => $pedido->prazo ?? now()->format('Y-m-d'),
        'horario' => '09:00',
        'items' => urlencode($pedido->itens ?? ''),
        'obs_retirada' => urlencode($pedido->obs_retirada ?? ''),
    ]) }}" 
    class="btn btn-primary" 
    style="padding: 5px 10px; border-radius: 5px;"
>
    Realizar Agendamento
</a>

                     <a href="{{ route('pagamento.index', ['cliente_id' => $pedido->cliente->id]) }}" class="btn btn-info">Ir para Pagamentos</a>
    </div>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
            </table>

            {{-- Modais de edição rápida --}}
            @foreach($pedidos as $pedido)
                <div 
                    x-show="openModal === {{ $pedido->id }}" 
                    x-transition 
                    x-cloak
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                >
               <div class="bg-white p-4 rounded shadow w-full max-w-lg max-h-[90vh] overflow-y-auto">
<h2 class="text-xl font-bold mb-2">Editar Pedido #{{ $pedido->id }}</h2>

<!-- Informações rápidas -->
<div class="mb-4 p-2 border rounded bg-gray-50">
    <p><strong>ID:</strong> {{ $pedido->id }}</p>
    <p><strong>Nome:</strong> {{ $pedido->cliente ? $pedido->cliente->nome : 'Não informado' }}</p>
    <p><strong>Endereço:</strong> {{ $pedido->cliente ? $pedido->cliente->endereco : 'Não informado' }}</p>
    
    @if($pedido->imagens->count())
        <div class="flex flex-wrap gap-2 mt-2">
            @foreach($pedido->imagens as $imagem)
                <img src="{{ asset('storage/' . $imagem->imagem) }}" alt="Imagem do pedido" class="w-6 h-6 object-cover rounded border" />
            @endforeach
        </div>
    @else
        <p class="text-sm text-gray-500 italic mt-2">Sem imagens</p>
    @endif
</div>

<form method="POST" action="{{ route('producao.update', $pedido->id) }}">
    @csrf
    @method('PUT')
>@if($pedido->terceirizadas->count())
        {{ $pedido->terceirizadas->pluck('tipoServico')->implode(', ') }}
    @else
        -
    @endif

{{-- Andamento e Tapeceiro lado a lado --}}
<div class="flex flex-wrap gap-4 mb-4">
    <div class="flex-1 min-w-[150px]">
        <label for="andamento_{{ $pedido->id }}" class="block font-bold">Andamento</label>
        <select name="andamento" id="andamento_{{ $pedido->id }}" class="w-full border rounded p-2">
            <option value="produzindo" {{ $pedido->andamento === 'produzindo' ? 'selected' : '' }}>Produzindo</option>
            <option value="retirar" {{ $pedido->andamento === 'retirar' ? 'selected' : '' }}>Retirar</option>
            <option value="montado" {{ $pedido->andamento === 'montado' ? 'selected' : '' }}>Montado</option>
            <option value="desmanchado" {{ $pedido->andamento === 'desmanchado' ? 'selected' : '' }}>Desmanchado</option>
            <option value="entregar" {{ $pedido->andamento === 'entregar' ? 'selected' : '' }}>Entregar</option>
            <option value="concluído" {{ $pedido->andamento === 'concluído' ? 'selected' : '' }}>Concluído</option>
        </select>
    </div>

    <div class="flex-1 min-w-[150px]">
        <label for="tapeceiro_{{ $pedido->id }}" class="block font-bold">Tapeceiro</label>
        <select name="tapeceiro" id="tapeceiro_{{ $pedido->id }}" class="w-full border rounded p-2">
            <option value="">-- Selecione o Tapeceiro --</option>
            <option value="André" {{ $pedido->tapeceiro === 'André' ? 'selected' : '' }}>André</option>
            <option value="Samuel" {{ $pedido->tapeceiro === 'Samuel' ? 'selected' : '' }}>Samuel</option>
            <option value="Paulo" {{ $pedido->tapeceiro === 'Paulo' ? 'selected' : '' }}>Paulo</option>
            <option value="Adailton" {{ $pedido->tapeceiro === 'Adailton' ? 'selected' : '' }}>Adailton</option>
            <option value="Distribuir" {{ $pedido->tapeceiro === 'Distribuir' ? 'selected' : '' }}>Distribuir</option>
        </select>
    </div>
</div>

    
    {{-- Datas lado a lado --}}
<div class="flex flex-wrap gap-4 mb-4">

    <div class="flex-1 min-w-[150px]">
        <label for="data_retirada_{{ $pedido->id }}" class="block font-bold text-sm mb-1">Data Retirada</label>
        <input 
            type="date" 
            name="data_retirada" 
            id="data_retirada_{{ $pedido->id }}" 
            value="{{ $pedido->data_retirada }}" 
            class="w-full border rounded p-2"
        >
    </div>

    <div class="flex-1 min-w-[150px]">
        <label for="obs_retirada_{{ $pedido->id }}" class="block font-bold text-sm mb-1">Observação Retirada</label>
        <textarea 
            name="obs_retirada" 
            id="obs_retirada_{{ $pedido->id }}" 
            rows="3" 
            class="w-full border rounded p-2"
        >{{ $pedido->obs_retirada ?? '' }}</textarea>
    </div>

    <div class="flex-1 min-w-[150px]">
        <label for="data_inicio_{{ $pedido->id }}" class="block font-bold text-sm mb-1">Data Início</label>
        <input 
            type="date" 
            name="data_inicio" 
            id="data_inicio_{{ $pedido->id }}" 
            value="{{ $pedido->data_inicio }}" 
            class="w-full border rounded p-2"
        >
    </div>
    <div class="flex-1 min-w-[150px]">
        <label for="pronto_dia_{{ $pedido->id }}" class="block font-bold text-sm mb-1">Pronto Dia</label>
        <input 
        type="date" 
        name="pronto_dia" 
        id="pronto_dia_{{ $pedido->id }}" 
        value="{{ $pedido->data_termino }}" 
        class="w-full border rounded p-2"
        >
    </div>
    <div class="flex-1 min-w-[150px]">
        <label for="previsao_entrega_{{ $pedido->id }}" class="block font-bold text-sm mb-1">Previsão Entrega</label>
        <input 
        type="date" 
        name="previsao_entrega" 
        id="previsao_entrega_{{ $pedido->id }}" 
        value="{{ $pedido->data_previsao }}" 
        class="w-full border rounded p-2"
        >
    </div>
    <div class="flex-1 min-w-[150px]">
        <label for="prazo_{{ $pedido->id }}" class="block font-bold text-sm mb-1">Prazo</label>
        <input 
            type="text" 
            name="prazo" 
            id="prazo_{{ $pedido->id }}" 
            value="{{ $pedido->prazo }}" 
            class="w-full border rounded p-2"
        >
    </div>
</div>
<div class="mb-4">
    <label for="observacao_{{ $pedido->id }}" class="block font-bold">Observação</label>
    <textarea 
        name="observacao" 
        id="observacao_{{ $pedido->id }}" 
        rows="3" 
        class="w-full border rounded p-2"
    >{{ $pedido->obs }}</textarea>
</div>

    <div class="flex justify-between mt-4">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            💾 Salvar
        </button>
        <button type="button" @click="openModal = null" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            ✖ Cancelar
        </button>
    </div>
</form>

                    </div>
                </div>
            @endforeach

        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }


        
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

        /* Estilo dos botões "Ver" e "Editar" */
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
            color: white;
        }

        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: auto;
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

        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
        }
    </style>
</x-app-layout>
