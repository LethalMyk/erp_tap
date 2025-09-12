<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parcela;

class ParcelaController extends Controller
{
public function update(Request $request, $id)
{
    $parcela = Parcela::findOrFail($id);

    $parcela->descricao = $request->descricao;
    $parcela->valor_parcela = $request->valor;
    $parcela->data_vencimento = $request->data_vencimento;
    $parcela->data_pagamento = $request->data_pagamento;
    $parcela->forma_pagamento = $request->forma_pagamento;

    if ($request->hasFile('comprovantes')) {
        $paths = [];
        foreach ($request->file('comprovantes') as $file) {
            $paths[] = $file->store('comprovantes', 'public');
        }
        $parcela->comprovante = implode(',', $paths);
    }

    $parcela->save();

    return redirect()->back()->with('success', 'Parcela atualizada com sucesso!');
}



}
