<?php

namespace App\Repositories;

use App\Models\Profissional;

class ProfissionalRepository
{
    public function all()
    {
        return Profissional::all();
    }

    public function find($id)
    {
        return Profissional::findOrFail($id);
    }

    public function create(array $data)
    {
        return Profissional::create($data);
    }

    public function update(Profissional $profissional, array $data)
    {
        return $profissional->update($data);
    }

    public function delete(Profissional $profissional)
    {
        return $profissional->delete();
    }
}
