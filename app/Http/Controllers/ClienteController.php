<?php

namespace App\Http\Controllers;

use App\Models\Cliente;  // Alterado para Cliente
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    // Exibe a lista de clientes
    public function index()
    {
        $clientes = Cliente::all();  // Usando Cliente
        return view('clientes.index', compact('clientes'));
    }

    // Exibe o formulário para criar um novo cliente
 public function store(Request $request)
{
    $request->validate([
        'nome' => 'required|string|max:255',
        'telefone' => 'nullable|string|max:15',
        'endereco' => 'nullable|string|max:255',
        'email' => 'nullable|string|email|max:255',
        'cpf' => 'nullable|string|max:14',
    ]);

    // Criando o cliente
    $cliente = Cliente::create([
        'nome' => $request->nome,
        'telefone' => $request->telefone,
        'endereco' => $request->endereco,
        'email' => $request->email,
        'cpf' => $request->cpf,
    ]);

    // Redirecionando para a página de edição do cliente recém-criado
   return redirect()->route('clientes.edit', ['cliente' => $cliente->id])
                 ->with('success', 'Cliente criado com sucesso!');

}


    // Exibe os detalhes de um cliente específico
  public function show($id)
{
    $cliente = Cliente::find($id);
    if ($cliente) {
        return view('clientes.show', compact('cliente'));
    } else {
        return redirect('/clientes')->with('error', 'Cliente não encontrado.');
    }
}


    // Exibe o formulário para editar um cliente específico
  public function edit(Cliente $cliente)
{
    return view('clientes.edit', compact('cliente'));
}


    // Atualiza o cliente no banco de dados
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:15',
            'endereco' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'cpf' => 'nullable|string|max:14',
        ]);

        $cliente = Cliente::findOrFail($id);  // Usando Cliente
        $cliente->update([
            'nome' => $request->nome,
            'telefone' => $request->telefone,
            'endereco' => $request->endereco,
            'email' => $request->email,
            'cpf' => $request->cpf,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    // Exclui um cliente do banco de dados
    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);  // Usando Cliente
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente excluído com sucesso!');
    }
    public function create()
{
    return view('clientes.create'); // Certifique-se de que o arquivo create.blade.php existe na pasta resources/views/clientes
}
}
