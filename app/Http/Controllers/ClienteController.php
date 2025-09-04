<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Services\ClienteService;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected $clienteService;

    public function __construct(ClienteService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    public function index()
    {
        $clientes = $this->clienteService->listarTodos();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:15',
            'endereco' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'cpf' => 'nullable|string|max:14',
        ]);

        $cliente = $this->clienteService->criar($dados);

        return redirect()->route('clientes.edit', $cliente->id)
                         ->with('success', 'Cliente criado com sucesso!');
    }

    public function show($id)
    {
        $cliente = $this->clienteService->buscarPorId($id);

        if (!$cliente) {
            return redirect()->route('clientes.index')->with('error', 'Cliente não encontrado.');
        }

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:15',
            'endereco' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255',
            'cpf' => 'nullable|string|max:14',
        ]);

        $this->clienteService->atualizar($cliente, $dados);

        return redirect()->route('clientes.index')->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $this->clienteService->deletar($cliente);

        return redirect()->route('clientes.index')->with('success', 'Cliente excluído com sucesso!');
    }
}
