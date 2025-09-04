<?php

namespace App\Repositories;

use App\Models\Cliente;

class ClienteRepository
{
    public function all()
    {
        return Cliente::all();
    }

    public function find($id)
    {
        return Cliente::find($id);
    }

    public function create(array $dados)
    {
        return Cliente::create($dados);
    }

    public function update(Cliente $cliente, array $dados)
    {
        return $cliente->update($dados);
    }

    public function delete(Cliente $cliente)
    {
        return $cliente->delete();
    }
}
