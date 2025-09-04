<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Item;
use App\Models\Terceirizada;
use Illuminate\Http\Request;
use App\Services\TerceirizadaService;

class TerceirizadaController extends Controller
{
    protected $service;

    public function __construct(TerceirizadaService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $terceirizadas = $this->service->listar();
        return view('terceirizadas.index', compact('terceirizadas'));
    }

    public function create()
    {
        $pedidos = Pedido::all();
        return view('terceirizadas.create', compact('pedidos'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'tipoServico' => 'required|in:Impermeabilizar,Higienizar,Pintar,Invernizar,Outros',
            'obs' => 'nullable|string',
            'item_id' => 'required|exists:items,id',
            'pedido_id' => 'required|exists:pedidos,id',
            'andamento' => 'required|in:em espera,executado,pronto',
            'valor' => 'nullable|numeric|min:0',
            'statusPg' => 'required|in:Pendente,Pago,Parcial',
        ]);

        $this->service->criar($dados);

        return redirect()->route('pedido.visualizar', $dados['pedido_id'])
                         ->with('success', 'Serviço terceirizado adicionado com sucesso!');
    }

    public function edit($id)
    {
        $terceirizada = $this->service->listar()->find($id);
        $pedidos = Pedido::all();
        $pedidoSelecionado = Pedido::find($terceirizada->pedido_id);

        return view('terceirizadas.edit', compact('terceirizada', 'pedidos', 'pedidoSelecionado'));
    }

    public function update(Request $request, Terceirizada $terceirizada)
    {
        $dados = $request->validate([
            'tipoServico' => 'required|in:Impermeabilizar,Higienizar,Pintar,Invernizar,Outros',
            'obs' => 'nullable|string',
            'item_id' => 'required|exists:items,id',
            'pedido_id' => 'required|exists:pedidos,id',
            'andamento' => 'required|in:em espera,executado,pronto',
            'valor' => 'nullable|numeric|min:0',
            'statusPg' => 'required|in:Pendente,Pago,Parcial',
        ]);

        $this->service->atualizar($terceirizada, $dados);

        return redirect()->route('terceirizadas.index')->with('success', 'Serviço terceirizado atualizado com sucesso!');
    }

    public function destroy(Terceirizada $terceirizada)
    {
        $this->service->remover($terceirizada);
        return redirect()->back()->with('success', 'Serviço terceirizado removido com sucesso!');
    }

    public function getItems($pedido_id)
    {
        $items = Item::where('pedido_id', $pedido_id)->get(['id', 'nomeItem as descricao']); 
        return response()->json($items);
    }
}
