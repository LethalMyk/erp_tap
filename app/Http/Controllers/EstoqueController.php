<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Estoque;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    /**
     * Exibe a lista de produtos no estoque
     */
    public function index()
    {
        $estoques = Estoque::with('produto', 'movimentos')->get();
        $produtos = Produto::all();

        return view('estoque.index', compact('estoques', 'produtos'));
    }

    /**
     * Adiciona um novo produto ao estoque
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'produto_existente' => 'nullable|exists:produtos,id',
            'nome' => 'nullable|string|max:255',
            'categoria' => 'required|string|max:255',
            'sub_categoria' => 'nullable|string|max:255',
            'unidade_medida' => 'required|string|max:50',
            'quantidade_inicial' => 'nullable|integer|min:0',
            'descricao' => 'nullable|string|max:1000',
        ]);

        $produto = $this->getOrCreateProduto($data);

        $estoque = Estoque::firstOrCreate(['produto_id' => $produto->id]);

        if (!empty($data['quantidade_inicial']) && $data['quantidade_inicial'] > 0) {
            $estoque->adicionar($data['quantidade_inicial'], 'Quantidade inicial');
        }

        return redirect()->route('estoque.index')->with('success', 'Produto adicionado ao estoque!');
    }

    /**
     * Retorna um produto existente ou cria um novo
     */
    private function getOrCreateProduto(array $data)
    {
        if (!empty($data['produto_existente'])) {
            return Produto::findOrFail($data['produto_existente']);
        }

        return Produto::create([
            'nome' => $data['nome'],
            'categoria' => $data['categoria'],
            'sub_categoria' => $data['sub_categoria'] ?? null,
            'unidade_medida' => $data['unidade_medida'],
            'descricao' => $data['descricao'] ?? null,
        ]);
    }
}
