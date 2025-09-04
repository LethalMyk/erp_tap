<?php

namespace App\Repositories;

use App\Models\Servico;

class ServicoRepository
{
    public function all()
    {
        return Servico::with('profissional', 'pedido')->get();
    }

    public function find($id)
    {
        return Servico::findOrFail($id);
    }

    public function create(array $data)
    {
        return Servico::create($data);
    }

    public function update(Servico $servico, array $data)
    {
        return $servico->update($data);
    }

    public function delete(Servico $servico)
    {
        return $servico->delete();
    }
}
