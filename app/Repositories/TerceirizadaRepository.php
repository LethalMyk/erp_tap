<?php

namespace App\Repositories;

use App\Models\Terceirizada;

class TerceirizadaRepository
{
    public function all()
    {
        return Terceirizada::with('item')->get();
    }

    public function find($id)
    {
        return Terceirizada::findOrFail($id);
    }

  public function create(array $data)
{
    // Garante que statusPg sempre tenha um valor válido
    if (!isset($data['statusPg']) || !in_array($data['statusPg'], ['Pendente','Pago','Parcial'])) {
        $data['statusPg'] = 'Pendente';
    }

    // Também pode garantir andamento
    if (!isset($data['andamento']) || !in_array($data['andamento'], ['em espera','executado','pronto'])) {
        $data['andamento'] = 'em espera';
    }

    return Terceirizada::create($data);
}


    public function update(Terceirizada $terceirizada, array $data)
    {
        return $terceirizada->update($data);
    }

    public function delete(Terceirizada $terceirizada)
    {
        return $terceirizada->delete();
    }
}
