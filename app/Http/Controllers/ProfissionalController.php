<?php

namespace App\Http\Controllers;

use App\Models\Profissional;
use Illuminate\Http\Request;

class ProfissionalController extends Controller
{
  public function index()
{
    $profissional = Profissional::all();
    return view('profissional.index', compact('profissional'));
}


    public function create()
    {
        return view('profissional.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
        ]);

        Profissional::create($request->all());

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
        $request->validate([
            'nome' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
        ]);

        $profissional->update($request->all());

        return redirect()->route('profissional.index')->with('success', 'Profissional atualizado com sucesso.');
    }

    public function destroy(Profissional $profissional)
    {
        $profissional->delete();

        return redirect()->route('profissional.index')->with('success', 'Profissional excluído com sucesso.');
    }
}
