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
        $clientes = $this->clienteService->listarTodos(); // pega todos os clientes

        return view('formulario', compact('profissionais', 'clientes'));
    }

    /**
     * Salva formulário completo (cliente + pedido + itens + pagamentos + imagens + agendamento)
     */
    public function store(Request $request)
    {
        $data = $request->all();

        // Função para limpar valores monetários
        $limparValor = function($valor) {
            $numero = str_replace(['R$', ' ', '.'], '', $valor); // remove R$, espaços e pontos
            $numero = str_replace(',', '.', $numero); // substitui vírgula por ponto
            return floatval($numero);
        };

        // Limpar valor total do pedido
        if (!empty($data['valor'])) {
            $data['valor'] = $limparValor($data['valor']);
        }

        // Limpar valores dos pagamentos
        if (!empty($data['pagamentos'])) {
            foreach ($data['pagamentos'] as $key => $pag) {
                if (!empty($pag['valor'])) {
                    $data['pagamentos'][$key]['valor'] = $limparValor($pag['valor']);
                }
            }
        }
// Garantir que valor_sugerido dos itens seja float
if (!empty($data['items'])) {
    foreach ($data['items'] as $key => $item) {

        if (isset($item['valor_sugerido'])) {
            $data['items'][$key]['valor_sugerido'] =
                floatval($item['valor_sugerido']);
        }
    }
}

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
