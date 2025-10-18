<?php

namespace App\Http\Controllers;

use App\Services\DespesaService;
use App\Repositories\DespesaRepository;
use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Parcela;
use Illuminate\Support\Facades\Storage;

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
     * Lista de despesas com filtros e ordenação
     */
public function index(Request $request)
{
    // Captura filtros compatíveis com o repository
    $filters = $request->only([
        'descricao', 'categoria', 'status', 'forma_pagamento',
        'sort', 'direction', 'per_page'
    ]);

    // Filtros de data
    $filters['parcela_data_inicio'] = $request->input('parcela_data_inicio');
    $filters['parcela_data_fim'] = $request->input('parcela_data_fim');

    // Busca despesas aplicando filtros e ordenação
    $despesas = $this->repository->all($filters);

    // Lista fixa de categorias
    $categorias = [
        (object)['id' => 'FORNECEDOR', 'nome' => 'Fornecedor'],
        (object)['id' => 'AGUA', 'nome' => 'Água'],
        (object)['id' => 'LUZ', 'nome' => 'Luz'],
        (object)['id' => 'MATERIAL', 'nome' => 'Material'],
        (object)['id' => 'PARTICULAR', 'nome' => 'Particular'],
        (object)['id' => 'OUTROS', 'nome' => 'Outros'],
    ];

    // Retorna a view com tudo pronto
    return view('despesas.index', compact('despesas', 'categorias'));
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
        $validated = $this->validateDespesa($request);

        $comprovantePath = $request->hasFile('comprovante')
            ? $request->file('comprovante')->store('comprovantes', 'public')
            : null;

        $validated['produtos'] = $this->agruparProdutos($validated);

        // Adiciona dados das parcelas
        $validated['parcelas_valor'] = $request->input('parcelas_valor', []);
        $validated['parcelas_descricao'] = $request->input('parcelas_descricao', []);
        $validated['parcelas_forma_pagamento'] = $request->input('parcelas_forma_pagamento', []);
        $validated['data_vencimento'] = $request->input('data_vencimento', []);
        $validated['chave_pagamento'] = $request->input('chave_pagamento', []);
        $validated['parcelas_comprovantes'] = $request->file('parcelas_comprovantes', []);

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

        $validated = $this->validateDespesa($request);

        $comprovantePath = $request->hasFile('comprovante')
            ? $request->file('comprovante')->store('comprovantes', 'public')
            : null;

        $validated['produtos'] = $this->agruparProdutos($validated);

        $validated['parcelas_valor'] = $request->input('parcelas_valor', []);
        $validated['parcelas_descricao'] = $request->input('parcelas_descricao', []);
        $validated['parcelas_forma_pagamento'] = $request->input('parcelas_forma_pagamento', []);
        $validated['data_vencimento'] = $request->input('data_vencimento', []);
        $validated['chave_pagamento'] = $request->input('chave_pagamento', []);
        $validated['parcelas_comprovantes'] = $request->file('parcelas_comprovantes', []);

        $this->service->atualizarDespesa($validated, $despesa, $comprovantePath);

        return redirect()->route('despesas.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    /**
     * Registrar pagamento de parcela
     */
    public function registrarPagamento(Request $request, $id)
    {
        $parcela = Parcela::findOrFail($id);

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

    /**
     * Validação comum para criação e atualização
     */
    protected function validateDespesa(Request $request)
    {
        return $request->validate([
            'data' => 'sometimes|required|date',
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
            'parcelas_valor.*' => 'nullable|numeric|min:0',
            'parcelas_descricao.*' => 'nullable|string|max:255',
            'parcelas_forma_pagamento.*' => 'nullable|string|max:50',
            'data_vencimento.*' => 'nullable|date',
        ]);
    }

    /**
     * Agrupa produtos iguais
     */
    protected function agruparProdutos(array $validated)
    {
        $produtos = [];
        if (isset($validated['produtos_id'])) {
            foreach ($validated['produtos_id'] as $i => $id) {
                $nome = $validated['produtos_novo'][$i] ?? null;
                $categoria = $validated['produtos_categoria'][$i] ?? '';
                $subcategoria = $validated['produtos_sub_categoria'][$i] ?? '';
                $quantidade = $validated['produtos_quantidade'][$i] ?? 0;
                $valor_unitario = $validated['produtos_valor_unitario'][$i] ?? 0;
                $valor_total = $validated['produtos_valor_total'][$i] ?? 0;

                $key = ($id ?? $nome) . '|' . $subcategoria;

                if (isset($produtos[$key])) {
                    $produtos[$key]['quantidade'] += $quantidade;
                    $produtos[$key]['valor_total'] += $valor_total;
                } else {
                    $produtos[$key] = compact(
                        'id', 'nome', 'categoria', 'subcategoria', 'quantidade', 'valor_unitario', 'valor_total'
                    );
                }
            }
        }
        return $produtos;
    }

    /**
     * Atualiza uma parcela (modal)
     */
    public function updateParcela(Request $request, $id)
    {
        $parcela = Parcela::findOrFail($id);

        $validated = $request->validate([
            'descricao' => 'nullable|string|max:255',
            'valor' => 'nullable|numeric|min:0',
            'data_vencimento' => 'nullable|date',
            'forma_pagamento' => 'nullable|string|max:50',
            'data_pagamento' => 'nullable|date',
            'comprovantes.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        if (array_key_exists('descricao', $validated)) {
            $parcela->descricao = $validated['descricao'];
        }
        if (array_key_exists('valor', $validated)) {
            $parcela->valor_parcela = $validated['valor'];
        }
        if (array_key_exists('data_vencimento', $validated)) {
            $parcela->data_vencimento = $validated['data_vencimento'];
        }
        if (array_key_exists('forma_pagamento', $validated)) {
            $parcela->forma_pagamento = $validated['forma_pagamento'];
        }

        if (!empty($validated['data_pagamento'])) {
            $parcela->data_pagamento = $validated['data_pagamento'];
            $parcela->status = 'PAGO';
        }

        if ($request->hasFile('comprovantes')) {
            $paths = $parcela->comprovante ? (array) json_decode($parcela->comprovante, true) : [];
            foreach ($request->file('comprovantes') as $file) {
                $paths[] = $file->store('comprovantes', 'public');
            }
            $parcela->comprovante = json_encode($paths);
        }

        $parcela->save();

        return redirect()->back()->with('success', 'Parcela atualizada com sucesso!');
    }
}
