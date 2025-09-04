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
