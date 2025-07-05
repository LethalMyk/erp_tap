<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class AgendamentoController extends Controller
{
    public function index()
    {
        $hoje = Carbon::today();
        $fimSemana = $hoje->copy()->endOfWeek();
        $fimDuasSemanas = $hoje->copy()->addWeeks(2)->endOfWeek();

        return view('agendamentos.index', [
            'agendamentosHoje' => Agendamento::whereDate('data', $hoje)->orderBy('horario')->get(),

            'agendamentosSemana' => Agendamento::whereBetween('data', [$hoje, $fimSemana])
                ->whereDate('data', '!=', $hoje)
                ->orderBy('data')
                ->orderBy('horario')
                ->get(),

            'agendamentosProximasSemanas' => Agendamento::whereBetween('data', [$fimSemana->addDay(), $fimDuasSemanas])
                ->orderBy('data')
                ->orderBy('horario')
                ->get(),

            'agendamentosFuturos' => Agendamento::where('data', '>', $fimDuasSemanas)
                ->orderBy('data')
                ->orderBy('horario')
                ->get(),

            'agendamentosPassados' => Agendamento::where('data', '<', $hoje)
                ->orderByDesc('data')
                ->limit(10)
                ->get(),
        ]);
    }

 public function create(Request $request)
{
    $data = $request->input('data');      // yyyy-mm-dd
    $horario = $request->input('horario'); // hh:mm

    return view('agendamentos.create', compact('data', 'horario'));
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

        ]);

        Agendamento::create($request->all());

        return redirect()->route('agendamentos.index')->with('success', 'Agendamento criado com sucesso!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit($id)
{
    $agendamento = Agendamento::findOrFail($id);
    return view('agendamentos.edit', compact('agendamento'));
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $request->validate([
        'tipo' => 'required|in:entrega,retirada,assistencia,orcamento',
        'data' => 'required|date',
        'horario' => 'required',
        'nome_cliente' => 'required|string',
        'endereco' => 'required|string',
        'status' => 'required|in:pendente,realizado,cancelado',
        'telefone' => 'nullable|string|max:20',

    ]);

    $agendamento = Agendamento::findOrFail($id);
    $agendamento->update($request->all());

    return redirect()->route('agendamentos.index')->with('success', 'Agendamento atualizado!');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
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
    // Define cor baseada no tipo
    $coresPorTipo = [
        'entrega' => '#007bff',     // azul
        'retirada' => '#ffc107',    // amarelo
        'orcamento' => '#6f42c1',   // roxo
        'assistencia' => '#e25822', // laranja
    ];

    $cor = $coresPorTipo[$ag->tipo] ?? '#007bff'; // default azul se tipo desconhecido

    return [
        'id' => $ag->id,
        'title' => $ag->nome_cliente,
        'start' => $ag->data . 'T' . $ag->horario,
        'color' => $cor,
        'status' => $ag->status,
        'tipo' => $ag->tipo,
        'endereco' => $ag->endereco,
        'telefone' => $ag->telefone ?? 'Não informado', // se tiver telefone no model
    ];
});


    return view('agendamentos.calendar', ['eventos' => $eventos]);
}


}
