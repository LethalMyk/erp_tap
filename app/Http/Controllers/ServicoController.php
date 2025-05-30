<?php

namespace App\Http\Controllers;

use App\Models\Servico;
use App\Models\Profissional;
use App\Models\Pedido;
use Illuminate\Http\Request;

class ServicoController extends Controller
{
    public function index()
    {
        $servicos = Servico::with('profissional', 'pedido')->get();
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
        $request->validate([
            'profissional_id' => 'required|exists:profissional,id',
            'pedido_id' => 'required|exists:pedidos,id',
            'data_inicio' => 'required|date',
            'dificuldade' => 'required|string',
            'data_previsao' => 'nullable|date',
            'obs' => 'nullable|string',
        ]);

        Servico::create($request->all());

        return redirect()->route('servico.index')->with('success', 'Serviço cadastrado com sucesso.');
    }

    public function show(Servico $servico)
    {
        return view('servico.show', compact('servico'));
    }

    public function edit(Servico $servico)
    {
        $profissionais = Profissional::all();
        $pedidos = Pedido::all();
        return view('servico.edit', compact('servico', 'profissionais', 'pedidos'));
    }

    public function update(Request $request, Servico $servico)
    {
       $request->validate([
    'profissional_id' => 'required|exists:profissional,id', // <- CORRETO
    'pedido_id' => 'required|exists:pedidos,id',
    'data_inicio' => 'required|date',
    'dificuldade' => 'required|string',
    'data_previsao' => 'nullable|date',
    'obs' => 'nullable|string',
]);


        $servico->update($request->all());

        return redirect()->route('servico.index')->with('success', 'Serviço atualizado com sucesso.');
    }

    public function destroy(Servico $servico)
    {
        $servico->delete();

        return redirect()->route('servico.index')->with('success', 'Serviço excluído com sucesso.');
    }
}
