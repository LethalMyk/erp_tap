<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\PedidoImagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Terceirizada;

class PedidoController extends Controller
{
    // Método para exibir a lista de pedidos
public function index(Request $request)
{
    // Captura os filtros da requisição
    $id = $request->input('id');
    $clienteNome = $request->input('cliente_nome');
    $endereco = $request->input('endereco');
    $telefone = $request->input('telefone');
    $data = $request->input('data');
    $andamento = $request->input('andamento');
    $tapeceiro = $request->input('tapeceiro');
    $mes = $request->input('mes'); // Filtro de mês agora incluído

    // Carregar pedidos com filtros aplicados
    $pedidos = Pedido::with(['cliente', 'imagens', 'servicos.profissional'])
        ->when($id, function($query) use ($id) {
            return $query->where('id', $id);
        })
        ->when($clienteNome, function($query) use ($clienteNome) {
            return $query->whereHas('cliente', function($query) use ($clienteNome) {
                $query->where('nome', 'like', '%' . $clienteNome . '%');
            });
        })
        ->when($endereco, function($query) use ($endereco) {
            return $query->whereHas('cliente', function($query) use ($endereco) {
                $query->where('endereco', 'like', '%' . $endereco . '%');
            });
        })
        ->when($telefone, function($query) use ($telefone) {
            return $query->whereHas('cliente', function($query) use ($telefone) {
                $query->where('telefone', 'like', '%' . $telefone . '%');
            });
        })
        ->when($data, function($query) use ($data) {
            return $query->whereDate('data', $data);
        })
        ->when($andamento, function($query) use ($andamento) {
            if (is_array($andamento)) {
                return $query->whereIn('andamento', $andamento);
            } else {
                return $query->where('andamento', 'like', '%' . $andamento . '%');
            }
        })
        ->when($tapeceiro, function($query) use ($tapeceiro) {
            return $query->whereHas('servicos.profissional', function($query) use ($tapeceiro) {
                $query->where('nome', 'like', '%' . $tapeceiro . '%');
            });
        })
        ->when($mes, function($query) use ($mes) {
            // Filtro de mês no data_termino dos serviços
            return $query->whereHas('servicos', function($query) use ($mes) {
                return $query->whereMonth('data_termino', '=', $mes);
            });
        })
        ->get();

    // Passa os filtros e pedidos para a view
    return view('pedidos.index', compact(
        'pedidos', 'id', 'clienteNome', 'endereco', 'telefone', 'data', 'andamento', 'tapeceiro', 'mes'
    ));
}



    // Método para exibir o formulário de criação de pedido
    public function create()
    {
        $clientes = Cliente::all();
        return view('pedidos.create', compact('clientes'));
    }

    // Método para armazenar um novo pedido
    // Método para armazenar um novo pedido
public function store(Request $request)
{
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'qntItens' => 'required|integer',
        'data' => 'required|date',
        'valor' => 'required|numeric',
        'obs' => 'nullable|string',
        'prazo' => 'required|date',
        'imagens.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
    ]);

    // Criar pedido com status "RESTA" e valorResta igual ao valor inicial
    $pedido = Pedido::create([
        'cliente_id' => $request->cliente_id,
        'qntItens' => $request->qntItens,
        'data' => $request->data,
        'valor' => $request->valor,
        'valorResta' => $request->valor, // Definir valorResta igual ao valor inicial
        'status' => 'RESTA', // Sempre inicia como RESTA
        'obs' => $request->obs,
        'prazo' => $request->prazo,
        'andamento' => 'Retirar', // Sempre inicia como RESTA
    ]);

    // Armazenamento de imagens (se houver)
    if ($request->hasFile('imagens')) {
        foreach ($request->file('imagens') as $imagem) {
            $path = $imagem->store('pedidos', 'public');
            PedidoImagem::create([
                'pedido_id' => $pedido->id,
                'imagem' => $path
            ]);
        }
    }

    return redirect()->route('pedidos.index')->with('success', 'Pedido criado com sucesso!');
}


    // Método para exibir o formulário de edição de pedido
    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::all();
        return view('pedidos.edit', compact('pedido', 'clientes'));
    }

    // Método para atualizar um pedido
    public function update(Request $request, Pedido $pedido)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'qntItens' => 'required|integer',
            'data' => 'required|date',
            'valor' => 'required|numeric',
            'status' => 'required|string',
            'obs' => 'nullable|string',
            'prazo' => 'required|date',
            'imagens.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $pedido->update($request->except('imagens'));

        if ($request->hasFile('imagens')) {
            foreach ($request->file('imagens') as $imagem) {
                $path = $imagem->store('pedidos', 'public');
                PedidoImagem::create([
                    'pedido_id' => $pedido->id,
                    'imagem' => $path
                ]);
            }
        }

        return redirect()->route('pedidos.index')->with('success', 'Pedido atualizado com sucesso!');
    }

    // Método para exibir os detalhes de um pedido
    public function show(Pedido $pedido)
    {
        return view('pedidos.show', compact('pedido'));
    }

    public function destroyImagem(Pedido $pedido, PedidoImagem $imagem)
{
    // Verifica se a imagem pertence ao pedido correto
    if ($imagem->pedido_id !== $pedido->id) {
        return redirect()->route('pedidos.show', $pedido->id)->with('error', 'Imagem não pertence a este pedido.');
    }

    // Exclui a imagem do armazenamento
    Storage::disk('public')->delete($imagem->imagem);

    // Remove do banco de dados
    $imagem->delete();

    return redirect()->route('pedidos.show', $pedido->id)->with('success', 'Imagem excluída com sucesso.');
}


    // Impressão da via padrão TAP
    public function imprimirviatap($id)
    {
        $pedido = Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
        return view('pedidos.vias.imprimirviatap', compact('pedido'));
    }

 // Impressão da via padrão Retirada
    public function imprimirviaretirada($id)
    {
        $pedido = Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
        return view('pedidos.vias.imprimirviaretirada', compact('pedido'));
    }

        public function imprimirviacompleta($id)
    {
        $pedido = Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
        return view('pedidos.vias.imprimirviacompleta', compact('pedido'));
    }
      public function imprimirviasimplificada($id)
    {
        $pedido = Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
        return view('pedidos.vias.imprimirviasimplificada', compact('pedido'));
    }
}
