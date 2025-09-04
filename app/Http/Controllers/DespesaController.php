<?php

namespace App\Http\Controllers;

use App\Models\Despesa;
use App\Models\Parcela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DespesaController extends Controller
{
    public function index(Request $request)
    {
        $query = Despesa::with('usuario', 'parcelas');

        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('forma_pagamento')) {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }

        $despesas = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());

        return view('despesas.index', compact('despesas'));
    }

    public function create()
    {
        return view('despesas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'categoria' => 'required|in:FORNECEDOR,AGUA,LUZ,MATERIAL,PARTICULAR,OUTROS',
            'forma_pagamento' => 'required|in:PIX,DINHEIRO,DÉBITO,CRÉDITO,TRANSFERÊNCIA,BOLETO,A PRAZO,CHEQUE,OUTROS',
            'parcelas_descricao' => 'nullable|array',
            'parcelas_valor' => 'nullable|array',
            'data_vencimento' => 'nullable|array',
            'chave_pagamento' => 'nullable|array',
            'comprovante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'observacao' => 'nullable|string',
        ]);

        $comprovantePath = null;
        if ($request->hasFile('comprovante')) {
            $comprovantePath = $request->file('comprovante')->store('comprovantes', 'public');
        }

        $pagamentosImediatos = ['PIX','DINHEIRO','DÉBITO','CRÉDITO','TRANSFERÊNCIA'];

        $nota = Despesa::create([
            'descricao' => $validated['descricao'],
            'valor_total' => $validated['valor'],
            'categoria' => $validated['categoria'],
            'forma_pagamento' => $validated['forma_pagamento'],
            'observacao' => $validated['observacao'] ?? null,
            'created_by' => Auth::id(),
        ]);

        if (in_array($validated['forma_pagamento'], $pagamentosImediatos)) {
            Parcela::create([
                'despesa_id' => $nota->id,
                'numero_parcela' => 1,
                'valor_parcela' => $validated['valor'],
                'data_vencimento' => $validated['data'],
                'data_pagamento' => now()->toDateString(),
                'status' => 'PAGO',
                'chave_pagamento' => null,
                'comprovante' => $comprovantePath,
            ]);
        } else {
            $parcelasDesc = $request->parcelas_descricao ?? [$validated['descricao']];
            $parcelasValor = $request->parcelas_valor ?? [$validated['valor']];
            $datas = $request->data_vencimento ?? [null];
            $chaves = $request->chave_pagamento ?? [null];

            foreach ($parcelasDesc as $index => $desc) {
                Parcela::create([
                    'despesa_id' => $nota->id,
                    'numero_parcela' => $index + 1,
                    'valor_parcela' => $parcelasValor[$index] ?? $validated['valor'],
                    'data_vencimento' => $datas[$index] ?? null,
                    'status' => 'PENDENTE',
                    'chave_pagamento' => $chaves[$index] ?? null,
                    'comprovante' => $comprovantePath,
                ]);
            }
        }

        return redirect()->route('despesas.index')->with('success', 'Despesa cadastrada com sucesso!');
    }

    public function edit(Despesa $despesa)
    {
        $despesa->load('parcelas');
        return view('despesas.edit', compact('despesa'));
    }

    public function update(Request $request, Despesa $despesa)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'categoria' => 'required|in:FORNECEDOR,AGUA,LUZ,MATERIAL,PARTICULAR,OUTROS',
            'forma_pagamento' => 'required|in:PIX,DINHEIRO,DÉBITO,CRÉDITO,TRANSFERÊNCIA,BOLETO,A PRAZO,CHEQUE,OUTROS',
            'observacao' => 'nullable|string',
            'comprovante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('comprovante')) {
            if ($despesa->parcelas()->exists()) {
                foreach ($despesa->parcelas as $parcela) {
                    if ($parcela->comprovante) {
                        Storage::disk('public')->delete($parcela->comprovante);
                    }
                    $parcela->update(['comprovante' => $request->file('comprovante')->store('comprovantes','public')]);
                }
            }
        }

        $despesa->update($validated);

        return redirect()->route('despesas.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    public function registrarPagamento(Request $request, $id)
    {
        $parcela = Parcela::findOrFail($id);

        $parcela->data_pagamento = $request->data_pagamento;
        $parcela->descricao = $request->descricao ?? $parcela->descricao;
        $parcela->status = 'PAGO';

        if($request->hasFile('comprovante')){
            $file = $request->file('comprovante');
            $path = $file->store('comprovantes', 'public');
            $parcela->comprovante = $path;
        }

        $parcela->save();

        return response()->json(['success' => true]);
    }

    public function destroy(Despesa $despesa)
    {
        foreach ($despesa->parcelas as $parcela) {
            if ($parcela->comprovante) {
                Storage::disk('public')->delete($parcela->comprovante);
            }
            $parcela->delete();
        }

        $despesa->delete();

        return redirect()->route('despesas.index')->with('success', 'Despesa excluída com sucesso!');
    }
}
