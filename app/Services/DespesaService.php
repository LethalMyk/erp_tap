<?php

namespace App\Http\Controllers;

use App\Models\Despesa;
use App\Models\Produto;
use Illuminate\Http\Request;
use App\Services\DespesaService;
use Illuminate\Support\Facades\Auth;

class DespesaController extends Controller
{
    protected DespesaService $despesaService;

    public function __construct(DespesaService $despesaService)
    {
        $this->despesaService = $despesaService;
    }

    /**
     * Lista despesas com filtros e paginação
     */
    public function index(Request $request)
    {
        $query = Despesa::with('usuario', 'parcelas', 'produtosComprados.produto');

        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', "%{$request->descricao}%");
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('forma_pagamento')) {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }

        $despesas = $query->orderBy('created_at', 'desc')
                          ->paginate(10)
                          ->appends($request->all());

        return view('despesas.index', compact('despesas'));
    }

    /**
     * Formulário de criação de despesa
     */
    public function create()
    {
        $produtos = Produto::orderBy('nome')->get();
        return view('despesas.create', compact('produtos'));
    }

    /**
     * Armazena uma nova despesa
     */
    public function store(Request $request)
    {
        $validated = $this->despesaService->validarDespesa($request);

        $comprovante = $request->file('comprovante');

        $this->despesaService->criarDespesa($validated, $comprovante, Auth::id());

        return redirect()->route('despesas.index')
                         ->with('success', 'Despesa cadastrada com sucesso!');
    }

    /**
     * Formulário de edição de despesa
     */
    public function edit(Despesa $despesa)
    {
        $despesa->load('parcelas', 'produtosComprados.produto');
        $produtos = Produto::orderBy('nome')->get();

        return view('despesas.edit', compact('despesa', 'produtos'));
    }

    /**
     * Atualiza uma despesa existente
     */
    public function update(Request $request, Despesa $despesa)
    {
        $validated = $this->despesaService->validarDespesa($request, $despesa);

        $comprovante = $request->file('comprovante');

        $this->despesaService->atualizarDespesa($despesa, $validated, $comprovante);

        return redirect()->route('despesas.index')
                         ->with('success', 'Despesa atualizada com sucesso!');
    }

    /**
     * Registrar pagamento de uma parcela
     */
    public function registrarPagamento(Request $request, int $parcelaId)
    {
        $this->despesaService->registrarPagamento($parcelaId, $request);

        return response()->json(['success' => true]);
    }

    /**
     * Excluir despesa
     */
    public function destroy(Despesa $despesa)
    {
        $this->despesaService->excluirDespesa($despesa);

        return redirect()->route('despesas.index')
                         ->with('success', 'Despesa excluída com sucesso!');
    }
}
