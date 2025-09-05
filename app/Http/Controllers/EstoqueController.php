<?php

namespace App\Http\Controllers;

use App\Repositories\EstoqueRepository;
use App\Services\EstoqueService;
use Illuminate\Http\Request;

class EstoqueController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(EstoqueRepository $repository, EstoqueService $service)
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $produtos = $this->repository->allDisponiveis();
        return view('estoque.index', compact('produtos'));
    }

    public function updateQuantidade(Request $request, $estoqueId)
    {
        $data = $request->validate([
            'quantidade_disponivel' => 'required|integer|min:0',
        ]);

        $this->service->atualizarQuantidade($estoqueId, $data['quantidade_disponivel']);

        return redirect()->route('estoque.index')->with('success', 'Quantidade atualizada com sucesso!');
    }
}
