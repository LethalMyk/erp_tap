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

    /**
     * Lista de despesas
     */
    public function index(Request $request)
    {
        $despesas = $this->repository->all($request->all());
        return view('despesas.index', compact('despesas'));
    }

    /**
     * Formulário de criação
     */
    public function create()
    {
        $produtos = Produto::all();
        return view('despesas.create', compact('produtos'));
    }

    /**
     * Armazenar nova despesa
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'descricao' => 'required|string|max:255',
            'valor_total' => 'required|numeric',
            'categoria' => 'required|in:FORNECEDOR,AGUA,LUZ,MATERIAL,PARTICULAR,OUTROS',
            'forma_pagamento' => 'required|in:À VISTA,A PRAZO',
            'observacao' => 'nullable|string',
            'produtos_id' => 'array',
            'produtos_novo' => 'array',
            'produtos_quantidade' => 'array',
            'produtos_valor_unitario' => 'array',
            'produtos_valor_total' => 'array',
            'produtos_categoria' => 'array',
            'produtos_sub_categoria' => 'array',
            'produtos_unidade_medida' => 'array',
            'produtos_quantidade.*' => 'nullable|numeric|min:0',
            'produtos_valor_unitario.*' => 'nullable|numeric|min:0',
            'produtos_valor_total.*' => 'nullable|numeric|min:0',
        ]);

        $comprovantePath = $request->hasFile('comprovante')
            ? $request->file('comprovante')->store('comprovantes', 'public')
            : null;

        // Agrupa produtos iguais
        $produtos = [];
        if(isset($validated['produtos_id'])){
            foreach ($validated['produtos_id'] as $i => $id) {
                $nome = $validated['produtos_novo'][$i] ?? null;
                $categoria = $validated['produtos_categoria'][$i] ?? '';
                $subcategoria = $validated['produtos_sub_categoria'][$i] ?? ''; // <- NOVO
                $quantidade = $validated['produtos_quantidade'][$i] ?? 0;
                $valor_unitario = $validated['produtos_valor_unitario'][$i] ?? 0;
                $valor_total = $validated['produtos_valor_total'][$i] ?? 0;

                $key = ($id ?? $nome) . '|' . $subcategoria; // agrupa considerando subcategoria
                if(isset($produtos[$key])){
                    $produtos[$key]['quantidade'] += $quantidade;
                    $produtos[$key]['valor_total'] += $valor_total;
                } else {
                    $produtos[$key] = compact(
                        'id','nome','categoria','subcategoria','quantidade','valor_unitario','valor_total'
                    );
                }
            }
        }

        $validated['produtos'] = $produtos;

        $this->service->criarDespesaComProdutos($validated, $comprovantePath);

        return redirect()->route('despesas.index')->with('success', 'Despesa cadastrada com sucesso!');
    }

    /**
     * Formulário de edição
     */
    public function edit($id)
    {
        $despesa = $this->repository->find($id);
        $produtos = Produto::all();
        return view('despesas.edit', compact('despesa', 'produtos'));
    }

    /**
     * Atualizar despesa
     */
    public function update(Request $request, $id)
    {
        $despesa = $this->repository->find($id);

        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor_total' => 'required|numeric',
            'categoria' => 'required|in:FORNECEDOR,AGUA,LUZ,MATERIAL,PARTICULAR,OUTROS',
            'forma_pagamento' => 'required|in:À VISTA,A PRAZO',
            'observacao' => 'nullable|string',
            'produtos_id' => 'array',
            'produtos_novo' => 'array',
            'produtos_quantidade' => 'array',
            'produtos_valor_unitario' => 'array',
            'produtos_valor_total' => 'array',
            'produtos_categoria' => 'array',
            'produtos_sub_categoria' => 'array',
            'produtos_unidade_medida' => 'array',
            'produtos_quantidade.*' => 'nullable|numeric|min:0',
            'produtos_valor_unitario.*' => 'nullable|numeric|min:0',
            'produtos_valor_total.*' => 'nullable|numeric|min:0',
        ]);

        $comprovantePath = $request->hasFile('comprovante')
            ? $request->file('comprovante')->store('comprovantes', 'public')
            : null;

        // Agrupa produtos iguais
        $produtos = [];
        if(isset($validated['produtos_id'])){
            foreach ($validated['produtos_id'] as $i => $id) {
                $nome = $validated['produtos_novo'][$i] ?? null;
                $categoria = $validated['produtos_categoria'][$i] ?? '';
                $subcategoria = $validated['produtos_sub_categoria'][$i] ?? '';
                $quantidade = $validated['produtos_quantidade'][$i] ?? 0;
                $valor_unitario = $validated['produtos_valor_unitario'][$i] ?? 0;
                $valor_total = $validated['produtos_valor_total'][$i] ?? 0;

                $key = ($id ?? $nome) . '|' . $subcategoria; // agrupa considerando subcategoria
                if(isset($produtos[$key])){
                    $produtos[$key]['quantidade'] += $quantidade;
                    $produtos[$key]['valor_total'] += $valor_total;
                } else {
                    $produtos[$key] = compact('id','nome','categoria','subcategoria','quantidade','valor_unitario','valor_total');
                }
            }
        }

        $validated['produtos'] = $produtos;

        $this->service->atualizarDespesa($validated, $despesa, $comprovantePath);

        return redirect()->route('despesas.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    /**
     * Registrar pagamento de parcela
     */
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

    /**
     * Excluir despesa
     */
    public function destroy($id)
    {
        $despesa = $this->repository->find($id);
        $this->service->excluirDespesa($despesa);

        return redirect()->route('despesas.index')->with('success', 'Despesa excluída com sucesso!');
    }
}
