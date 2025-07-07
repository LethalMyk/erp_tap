<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AgendamentoController extends Controller
{
    public function index()
    {
        $hoje = Carbon::today();

        // Funções auxiliares para buscar agendamentos e orçamentos por data (ou intervalo)
        $buscarAgendamentosExcetoOrcamento = fn($start, $end = null) => 
            $end 
                ? Agendamento::whereBetween('data', [$start, $end])
                    ->where('tipo', '!=', 'orcamento')
                    ->orderBy('data')
                    ->orderBy('horario')
                    ->get()
                : Agendamento::whereDate('data', $start)
                    ->where('tipo', '!=', 'orcamento')
                    ->orderBy('horario')
                    ->get();

        $buscarOrcamentos = fn($start, $end = null) =>
            $end
                ? Agendamento::whereBetween('data', [$start, $end])
                    ->where('tipo', 'orcamento')
                    ->orderBy('data')
                    ->orderBy('horario')
                    ->get()
                : Agendamento::whereDate('data', $start)
                    ->where('tipo', 'orcamento')
                    ->orderBy('horario')
                    ->get();

        // Preparar arrays para agendamentos e orçamentos por dia para os próximos 15 dias
        $agendamentosPorDia = [];
        $orcamentosPorDia = [];
        for ($i = 0; $i <= 14; $i++) {
            $data = $hoje->copy()->addDays($i)->toDateString();
            $agendamentosPorDia[$i] = $buscarAgendamentosExcetoOrcamento($data);
            $orcamentosPorDia[$i] = $buscarOrcamentos($data);
        }

        // Agendamentos futuros além dos 15 dias
        $dataLimite = $hoje->copy()->addDays(15)->toDateString();
        $agendamentosFuturos = Agendamento::where('data', '>', $dataLimite)
            ->where('tipo', '!=', 'orcamento')
            ->orderBy('data')
            ->orderBy('horario')
            ->get();

        $orcamentosFuturos = Agendamento::where('data', '>', $dataLimite)
            ->where('tipo', 'orcamento')
            ->orderBy('data')
            ->orderBy('horario')
            ->get();

        // Agendamentos passados (limitados a 10)
        $agendamentosPassados = Agendamento::where('data', '<', $hoje)
            ->where('tipo', '!=', 'orcamento')
            ->orderByDesc('data')
            ->orderBy('horario')
            ->limit(10)
            ->get();

        $orcamentosPassados = Agendamento::where('data', '<', $hoje)
            ->where('tipo', 'orcamento')
            ->orderByDesc('data')
            ->orderBy('horario')
            ->limit(10)
            ->get();

        return view('agendamentos.index', [
            'agendamentosPorDia' => $agendamentosPorDia,
            'orcamentosPorDia' => $orcamentosPorDia,
            'agendamentosFuturos' => $agendamentosFuturos,
            'orcamentosFuturos' => $orcamentosFuturos,
            'agendamentosPassados' => $agendamentosPassados,
            'orcamentosPassados' => $orcamentosPassados,
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->input('data');
        $horario = $request->input('horario');
        $clienteId = $request->input('cliente_id');

        $cliente = null;
        if ($clienteId) {
            $cliente = Cliente::find($clienteId);
        }

        return view('agendamentos.create', compact('data', 'horario', 'cliente'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:entrega,retirada,assistencia,orcamento',
            'data' => 'required|date',
            'horario' => 'required',
            'nome_cliente' => 'required|string',
            'endereco' => 'required|string',
            'telefone' => 'nullable|string|max:20',
            'itens' => 'nullable|string',
            'observacao' => 'nullable|string',
        ]);

        Agendamento::create($request->all());

        return redirect()->back()->with('success', 'Agendamento criado com sucesso!');
    }

    public function edit($id)
    {
        $agendamento = Agendamento::findOrFail($id);
        return view('agendamentos.edit', compact('agendamento'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipo' => 'required|in:entrega,retirada,assistencia,orcamento',
            'data' => 'required|date',
            'horario' => 'required',
            'nome_cliente' => 'required|string',
            'endereco' => 'required|string',
            'telefone' => 'nullable|string|max:20',
            'itens' => 'nullable|string',
            'observacao' => 'nullable|string',
        ]);

        $agendamento = Agendamento::findOrFail($id);
        $agendamento->update($request->all());

        if ($request->input('redirect_to') === 'calendario') {
            return redirect()->route('agendamentos.calendario')->with('success', 'Agendamento atualizado!');
        }

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento atualizado!');
    }

    public function calendario(Request $request)
    {
        $query = Agendamento::query();

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $agendamentos = $query->get();

        $eventos = $agendamentos->map(function ($ag) {
            $coresPorTipo = [
                'entrega' => '#007bff',
                'retirada' => '#ffc107',
                'orcamento' => '#6f42c1',
                'assistencia' => '#e25822',
            ];

            return [
                'id' => $ag->id,
                'title' => $ag->nome_cliente,
                'start' => $ag->data . 'T' . $ag->horario,
                'color' => $coresPorTipo[$ag->tipo] ?? '#007bff',
                'status' => $ag->status ?? 'pendente',
                'tipo' => $ag->tipo,
                'endereco' => $ag->endereco,
                'telefone' => $ag->telefone ?? 'Não informado',
                'itens' => $ag->itens ?? '',
                'observacao' => $ag->observacao ?? '',
            ];
        });

        $cliente = null;
        $dataPreenchida = $request->input('data');
        $horarioPreenchido = $request->input('horario');

        if ($request->filled('cliente_id')) {
            $cliente = Cliente::find($request->cliente_id);
        }

        return view('agendamentos.calendar', [
            'eventos' => $eventos,
            'cliente' => $cliente,
            'dataPreenchida' => $dataPreenchida,
            'horarioPreenchido' => $horarioPreenchido,
        ]);
    }
}
