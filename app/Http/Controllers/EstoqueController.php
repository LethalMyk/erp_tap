<?php

namespace App\Http\Controllers;

use App\Repositories\EstoqueRepository;

class EstoqueController extends Controller
{
    protected $repository;

    public function __construct(EstoqueRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $produtos = $this->repository->allDisponiveis();
        return view('estoque.index', compact('produtos'));
    }
}
