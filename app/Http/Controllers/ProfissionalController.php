<?php

namespace App\Http\Controllers;

use App\Models\Profissional;
use Illuminate\Http\Request;
use App\Services\ProfissionalService;

class ProfissionalController extends Controller
{
    protected $service;

    public function __construct(ProfissionalService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $profissionais = $this->service->listar();
        return view('profissional.index', compact('profissionais'));
    }

    public function create()
    {
        return view('profissional.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
        ]);

        $this->service->criar($dados);

        return redirect()->route('profissional.index')->with('success', 'Profissional cadastrado com sucesso.');
    }

    public function show(Profissional $profissional)
    {
        return view('profissional.show', compact('profissional'));
    }

    public function edit(Profissional $profissional)
    {
        return view('profissional.edit', compact('profissional'));
    }

    public function update(Request $request, Profissional $profissional)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
        ]);

        $this->service->atualizar($profissional, $dados);

        return redirect()->route('profissional.index')->with('success', 'Profissional atualizado com sucesso.');
    }

    public function destroy(Profissional $profissional)
    {
        $this->service->remover($profissional);
        return redirect()->route('profissional.index')->with('success', 'Profissional excluído com sucesso.');
    }
}
