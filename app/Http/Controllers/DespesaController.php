<?php

namespace App\Http\Controllers;

use App\Models\Despesa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DespesaController extends Controller
{
    public function index(Request $request)
    {
        $query = Despesa::query();

        // Aplicar filtros se existirem
        if ($request->filled('descricao')) {
            $query->where('descricao', 'like', '%' . $request->descricao . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
        if ($request->filled('forma_pagamento')) {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }

        // Ordenação segura
        $sortable = ['id', 'descricao', 'valor', 'data_vencimento', 'data_pagamento', 'status', 'categoria', 'forma_pagamento', 'created_at'];
        $sort = $request->get('sort');
        $direction = $request->get('direction') === 'desc' ? 'desc' : 'asc';

        if (in_array($sort, $sortable)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('data_vencimento', 'desc');
        }

        $despesas = $query->paginate(10)->withQueryString();

        return view('despesas.index', compact('despesas'));
    }

    public function create()
    {
        return view('despesas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data_vencimento' => 'required|date',
            'data_pagamento' => 'nullable|date',
            'status' => 'required|in:PENDENTE,PAGO,ATRASADO',
            'categoria' => 'required|in:FORNECEDOR,AGUA,LUZ,MATERIAL,PARTICULAR,OUTROS',
            'forma_pagamento' => 'required|in:PIX,DINHEIRO,BOLETO,CARTAO,TRANSFERENCIA,OUTROS',
            'chave_pagamento' => 'nullable|string|max:255',
            'comprovante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'observacao' => 'nullable|string',
        ]);

        if ($request->hasFile('comprovante')) {
            $validated['comprovante'] = $request->file('comprovante')->store('comprovantes', 'public');
        }

        $validated['created_by'] = Auth::id();

        Despesa::create($validated);

        return redirect()->route('despesas.index')->with('success', 'Despesa cadastrada com sucesso!');
    }

    public function edit(Despesa $despesa)
    {
        return view('despesas.edit', compact('despesa'));
    }

    public function update(Request $request, Despesa $despesa)
    {
        $validated = $request->validate([
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric',
            'data_vencimento' => 'required|date',
            'data_pagamento' => 'nullable|date',
            'status' => 'required|in:PENDENTE,PAGO,ATRASADO',
            'categoria' => 'required|in:FORNECEDOR,AGUA,LUZ,MATERIAL,PARTICULAR,OUTROS',
            'forma_pagamento' => 'required|in:PIX,DINHEIRO,BOLETO,CARTAO,TRANSFERENCIA,OUTROS',
            'chave_pagamento' => 'nullable|string|max:255',
            'comprovante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'observacao' => 'nullable|string',
        ]);

        if ($request->hasFile('comprovante')) {
            // Apaga arquivo antigo se existir
            if ($despesa->comprovante) {
                Storage::disk('public')->delete($despesa->comprovante);
            }
            $validated['comprovante'] = $request->file('comprovante')->store('comprovantes', 'public');
        }

        $despesa->update($validated);

        return redirect()->route('despesas.index')->with('success', 'Despesa atualizada com sucesso!');
    }

    public function destroy(Despesa $despesa)
    {
        if ($despesa->comprovante) {
            Storage::disk('public')->delete($despesa->comprovante);
        }
        $despesa->delete();

        return redirect()->route('despesas.index')->with('success', 'Despesa excluída com sucesso!');
    }
}
