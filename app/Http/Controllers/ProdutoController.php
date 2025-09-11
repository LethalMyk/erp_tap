<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;

class ProdutoController extends Controller
{
    /**
     * Exibe a lista de produtos.
     */
    public function index()
    {
        $produtos = Produto::all();
        return view('produtos.index', compact('produtos'));
    }

    /**
     * Mostra o formulário para criar um novo produto.
     */
    public function create()
    {
        return view('produtos.create');
    }

    /**
     * Salva um novo produto no banco de dados.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'unidade_medida' => 'required|string|max:50',
            'categoria' => 'required|string|max:100',
            'sub_categoria' => 'nullable|string|max:100',
            'descricao' => 'nullable|string|max:1000',
        ]);

        Produto::create($request->all());

        return redirect()->route('produtos.index')->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Mostra um produto específico.
     */
    public function show(Produto $produto)
    {
        return view('produtos.show', compact('produto'));
    }

    /**
     * Mostra o formulário para editar um produto existente.
     */
    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    /**
     * Atualiza um produto existente no banco de dados.
     */
    public function update(Request $request, Produto $produto)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'unidade_medida' => 'required|string|max:50',
            'categoria' => 'required|string|max:100',
            'sub_categoria' => 'nullable|string|max:100',
            'descricao' => 'nullable|string|max:1000',
        ]);

        $produto->update($request->all());

        return redirect()->route('produtos.index')->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove um produto do banco de dados.
     */
    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()->route('produtos.index')->with('success', 'Produto removido com sucesso!');
    }
}
