<?php

namespace App\Http\Controllers;

use App\Services\DespesaService;
use App\Repositories\DespesaRepository;
use Illuminate\Http\Request;
use App\Models\Produto;

class DespesaController extends Controller
{
    protected DespesaService $service;
    protected DespesaRepository $repository;

    public function __construct(DespesaService $service, DespesaRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    // Lista de despesas
    public function index(Request $request)
    {
        $despesas = $this->repository->all($request->all());
        return view('despesas.index', compact('despesas'));
    }

    // Formulário de criação
    public function create()
    {
        $produtos = Produto::all();
        return view('despesas.create', compact('produtos'));
    }

    // Armazenar nova despesa
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'categoria' => 'required|in:FORNECEDOR,AGUA,LUZ,MATERIAL,PARTICULAR,OUTROS',
            'forma_pagamento' => 'required|in:À VISTA,A PRAZO',
            'produtos_id' => 'array',
            'produtos_novo' => 'array',
            'produtos_quantidade' => 'array',
            'produtos_valor_unitario' => 'array',
            'produtos_valor_total' => 'array',
        ]);

        $comprovantePath = $request->hasFile('comprovante')
            ? $request->file('comprovante')->store('comprovantes', 'public')
            : null;

        // Chama o service para criar a despesa com produtos
        $this->service->criarDespesaComProdutos($validated, $comprovantePath);

        return redirect()->route('despesas.index')->with('success', 'Despesa cadastrada com sucesso!');
    }

    // Atualizar despesa
    public function update(Request $request, $id)
    {
        $despesa = $this->repository->find($id);

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'categoria' => 'required|in:FORNECEDOR,AGUA,LUZ,MATERIAL,PARTICULAR,OUTROS',
            'forma_pagamento' => 'required|in:À VISTA,A PRAZO',
            'observacao' => 'nullable|string',
        ]);

        $comprovantePath = $request->hasFile('comprovante')
            ? $request->file('comprovante')->store('comprovantes', 'public')
            : null;

        $this->service->atualizarDespesa($validated, $despesa, $comprovantePath);

        return redirect()->route('despesas.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    // Registrar pagamento de parcela
    public function registrarPagamento(Request $request, $id)
    {
        $parcela = \App\Models\Parcela::findOrFail($id);

        $comprovantePath = $request->hasFile('comprovante')
            ? $request->file('comprovante')->store('comprovantes', 'public')
            : null;

        $this->service->registrarPagamentoParcela($parcela, [
            'data_pagamento' => $request->data_pagamento,
            'descricao' => $request->descricao,
            'comprovante' => $comprovantePath,
        ]);

        return response()->json(['success' => true]);
    }

    // Excluir despesa
    public function destroy($id)
    {
        $despesa = $this->repository->find($id);
        $this->service->excluirDespesa($despesa);

        return redirect()->route('despesas.index')->with('success', 'Despesa excluída com sucesso!');
    }
}
