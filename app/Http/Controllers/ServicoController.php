<?php

namespace App\Http\Controllers;

use App\Models\Profissional;
use App\Models\Pedido;
use App\Models\Servico;
use Illuminate\Http\Request;
use App\Services\ServicoService;

class ServicoController extends Controller
{
    protected $service;

    public function __construct(ServicoService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $servicos = $this->service->listar();
        return view('servico.index', compact('servicos'));
    }

    public function create()
    {
        $profissionais = Profissional::all();
        $pedidos = Pedido::all();
        return view('servico.create', compact('profissionais', 'pedidos'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'profissional_id' => 'required|exists:profissional,id',
            'pedido_id' => 'required|exists:pedidos,id',
            'data_inicio' => 'required|date',
            'dificuldade' => 'required|string',
            'data_previsao' => 'nullable|date',
            'obs' => 'nullable|string',
        ]);

        $this->service->criar($dados);

        return redirect()->route('servico.index')->with('success', 'Serviço cadastrado com sucesso.');
    }

    public function edit(Servico $servico)
    {
        $profissionais = Profissional::all();
        $pedidos = Pedido::all();
        return view('servico.edit', compact('servico', 'profissionais', 'pedidos'));
    }

    public function update(Request $request, Servico $servico)
    {
        $dados = $request->validate([
            'profissional_id' => 'required|exists:profissional,id',
            'pedido_id' => 'required|exists:pedidos,id',
            'data_inicio' => 'required|date',
            'dificuldade' => 'required|string',
            'data_previsao' => 'nullable|date',
            'obs' => 'nullable|string',
        ]);

        $this->service->atualizar($servico, $dados);

        return redirect()->route('servico.index')->with('success', 'Serviço atualizado com sucesso.');
    }

    public function destroy(Servico $servico)
    {
        $this->service->remover($servico);
        return redirect()->route('servico.index')->with('success', 'Serviço excluído com sucesso.');
    }

    public function show(Servico $servico)
    {
        return view('servico.show', compact('servico'));
    }
}
