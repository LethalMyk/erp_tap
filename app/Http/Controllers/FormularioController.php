<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PedidoService;
use App\Services\ClienteService;
use App\Models\Pedido;
use App\Models\Profissional;
use App\Models\PedidoImagem;

class FormularioController extends Controller
{
    protected $clienteService;
    protected $pedidoService;

    public function __construct(
        ClienteService $clienteService,
        PedidoService $pedidoService
    ) {
        $this->clienteService = $clienteService;
        $this->pedidoService = $pedidoService;
    }

    /**
     * Exibe formulário principal
     */
    public function index()
    {
        $profissionais = Profissional::orderBy('nome')->get();
        return view('formulario', compact('profissionais'));
    }

    /**
     * Salva formulário completo (cliente + pedido + itens + pagamentos + imagens + agendamento)
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $pedido = $this->pedidoService->criarPedidoCompleto($data);

        return redirect()->route('formulario.index')
            ->with('success', 'Formulário salvo com sucesso!');
    }

    /**
     * Visualiza pedido completo
     */
    public function visualizar($id)
    {
        $pedido = $this->pedidoService->getPedidoCompleto($id);
        return view('viewpedido', compact('pedido'));
    }

    /**
     * Adiciona imagens ao pedido
     */
    public function adicionarImagem(Request $request, Pedido $pedido)
    {
        $request->validate([
            'imagens' => 'required',
            'imagens.*' => 'image|max:5120',
        ]);

        $this->pedidoService->uploadImagens($pedido, $request->file('imagens'));

        return redirect()->back()->with('success', 'Imagens adicionadas com sucesso!');
    }

    /**
     * Remove imagem de pedido
     */
    public function removerImagem(PedidoImagem $imagem)
    {
        $this->pedidoService->removerImagem($imagem);

        return redirect()->back()->with('success', 'Imagem removida com sucesso!');
    }
}
