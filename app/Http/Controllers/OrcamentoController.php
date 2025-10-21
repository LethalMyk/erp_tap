<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrcamentoService;
use PDF; // <-- alias do DomPDF

class OrcamentoController extends Controller
{
    protected $orcamentoService;

    public function __construct(OrcamentoService $orcamentoService)
    {
        $this->orcamentoService = $orcamentoService;
    }

    /**
     * Lista todos os orçamentos
     */
 public function index()
{
    $orcamentos = $this->orcamentoService->listarOrcamentos();
    $clientes = $this->orcamentoService->listarClientes(); // ou Client::all()
    return view('orcamentos.index', compact('orcamentos', 'clientes'));
}

    /**
     * Formulário de criação de orçamento
     */
    public function create()
    {
        $dados = $this->orcamentoService->carregarOpcoes(); // retorna itens, tecidos, espumas, etc
        return view('orcamentos.create', $dados);
    }

    /**
     * Salva o orçamento no banco
     */
    public function store(Request $request)
    {
        $this->orcamentoService->criarOrcamento($request->all());
        return redirect()->route('orcamentos.index')->with('success', 'Orçamento criado com sucesso!');
    }

    /**
     * Exibe um orçamento específico
     */
    public function show($id)
    {
        $orcamento = $this->orcamentoService->find($id);
        return view('orcamentos.show', compact('orcamento'));
    }

    /**
     * Gera PDF do orçamento
     */
    public function pdf($id)
    {
        $orcamento = $this->orcamentoService->find($id);
        $pdf = PDF::loadView('orcamentos.pdf', compact('orcamento'))->setPaper('a4');
        return $pdf->download("ORC-{$orcamento->id}.pdf");
    }

    /**
     * Converte orçamento em pedido
     */
    public function converter($id)
    {
        $pedido = $this->orcamentoService->converterParaPedido($id);
        return redirect()->route('pedidos.show', $pedido->id)
            ->with('success', "Orçamento convertido para Pedido #{$pedido->id} com sucesso!");
    }
}
