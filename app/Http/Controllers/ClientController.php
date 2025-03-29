<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // Exibe a lista de clientes
    public function index()
    {
        $clients = Client::all();
        return view('clients.index', compact('clients'));
    }

    // Exibe o formulário de cadastro de novo cliente
    public function create()
    {
        return view('clients.create');
    }

    // Exibe os detalhes de um único cliente
    public function show($id)
    {
        $client = Client::findOrFail($id);  // Busca o cliente por ID
        return view('clients.show', compact('client'));  // Passa a variável $client para a view
    }

    // Armazena um novo cliente no banco
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:15',
            'endereco' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clientes',
            'cpf' => 'required|string|max:14|unique:clientes',
        ]);

        Client::create($request->all());  // Cria o cliente no banco de dados

        return redirect()->route('clients.index')->with('success', 'Cliente cadastrado com sucesso!');
    }

    // Exibe o formulário para editar um cliente
    public function edit($id)
    {
        $client = Client::findOrFail($id);  // Busca o cliente por ID
        return view('clients.edit', compact('client'));  // Passa a variável $client para a view
    }

    // Atualiza um cliente existente
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:15',
            'endereco' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clientes,email,' . $id,
            'cpf' => 'required|string|max:14|unique:clientes,cpf,' . $id,
        ]);

        $client = Client::findOrFail($id);  // Busca o cliente por ID
        $client->update($request->all());  // Atualiza os dados do cliente

        return redirect()->route('clients.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    // Exclui um cliente
    public function destroy($id)
    {
        $client = Client::findOrFail($id);  // Busca o cliente por ID
        $client->delete();  // Exclui o cliente do banco

        return redirect()->route('clients.index')->with('success', 'Cliente excluído com sucesso!');
    }
}
