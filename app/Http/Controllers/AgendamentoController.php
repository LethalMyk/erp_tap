<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AgendamentoService;
use App\Models\Cliente;
use App\Models\Agendamento;

class AgendamentoController extends Controller
{
    protected $service;

    public function __construct(AgendamentoService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $hoje = now()->startOfDay();

        $agendamentosPorDia = [];
        $orcamentosPorDia = [];
        for ($i = 0; $i <= 14; $i++) {
            $data = $hoje->copy()->addDays($i)->toDateString();
            $agendamentosPorDia[$i] = Agendamento::whereDate('data', $data)->where('tipo', '!=', 'orcamento')->get();
            $orcamentosPorDia[$i] = Agendamento::whereDate('data', $data)->where('tipo', 'orcamento')->get();
        }

        $agendamentosFuturos = Agendamento::where('data', '>', $hoje->copy()->addDays(15))->where('tipo', '!=', 'orcamento')->get();
        $orcamentosFuturos = Agendamento::where('data', '>', $hoje->copy()->addDays(15))->where('tipo', 'orcamento')->get();
        $agendamentosPassados = Agendamento::where('data', '<', $hoje)->where('tipo', '!=', 'orcamento')->orderByDesc('data')->limit(10)->get();
        $orcamentosPassados = Agendamento::where('data', '<', $hoje)->where('tipo', 'orcamento')->orderByDesc('data')->limit(10)->get();

        return view('agendamentos.index', compact(
            'agendamentosPorDia',
            'orcamentosPorDia',
            'agendamentosFuturos',
            'orcamentosFuturos',
            'agendamentosPassados',
            'orcamentosPassados'
        ));
    }

    public function create(Request $request)
    {
        $data = $request->input('data');
        $horario = $request->input('horario');
        $clienteId = $request->input('cliente_id');
        $cliente = $clienteId ? Cliente::find($clienteId) : null;
        $clientes = Cliente::all();

        return view('agendamentos.create', compact('data', 'horario', 'cliente', 'clientes'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'tipo' => 'required|in:entrega,retirada,assistencia,orcamento',
            'data' => 'required|date',
            'horario' => 'required',
            'cliente_id' => 'nullable|exists:clientes,id',
            'nome_cliente' => 'required_without:cliente_id|string',
            'endereco' => 'required_without:cliente_id|string',
            'telefone' => 'nullable|string|max:20',
            'itens' => 'nullable|string',
            'observacao' => 'nullable|string',
        ]);

        $this->service->criar($dados);

        return redirect()->back()->with('success', 'Agendamento criado com sucesso!');
    }

    public function edit($id)
    {
        $agendamento = $this->service->buscarPorId($id);
        return view('agendamentos.edit', compact('agendamento'));
    }

    public function update(Request $request, $id)
    {
        $agendamento = $this->service->buscarPorId($id);

        $dados = $request->validate([
            'tipo' => 'required|in:entrega,retirada,assistencia,orcamento',
            'data' => 'required|date',
            'horario' => 'required',
            'cliente_id' => 'nullable|exists:clientes,id',
            'nome_cliente' => 'required_without:cliente_id|string',
            'endereco' => 'required_without:cliente_id|string',
            'telefone' => 'nullable|string|max:20',
            'itens' => 'nullable|string',
            'observacao' => 'nullable|string',
        ]);

        $this->service->atualizar($agendamento, $dados);

        $redirect = $request->input('redirect_to') === 'calendario' ? 'agendamentos.calendario' : 'agendamentos.index';
        return redirect()->route($redirect)->with('success', 'Agendamento atualizado!');
    }

    public function destroy($id)
    {
        $agendamento = $this->service->buscarPorId($id);
        $this->service->deletar($agendamento);
        return redirect()->route('agendamentos.index')->with('success', 'Agendamento excluído com sucesso!');
    }

    public function calendario(Request $request)
    {
        $eventos = $this->service->eventos($request->tipo, $request->status);
        $cliente = $request->filled('cliente_id') ? Cliente::find($request->cliente_id) : null;
        $clientes = Cliente::all();
        $dataPreenchida = $request->input('data');
        $horarioPreenchido = $request->input('horario');

        $items = '';
        $obs_retirada = '';
        if ($cliente) {
            $itensData = $this->service->getItensCliente($cliente->id);
            $items = $itensData['itens'];
            $obs_retirada = $itensData['observacao'];
        }

        return view('agendamentos.calendar', compact('eventos', 'cliente', 'clientes', 'dataPreenchida', 'horarioPreenchido', 'items', 'obs_retirada'));
    }

    public function getItensCliente($id)
    {
        return response()->json($this->service->getItensCliente($id));
    }
}
