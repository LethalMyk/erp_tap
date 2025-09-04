<?php

namespace App\Repositories;

use App\Models\Agendamento;

class AgendamentoRepository
{
    public function all()
    {
        return Agendamento::all();
    }

    public function find($id)
    {
        return Agendamento::find($id);
    }

    public function create(array $dados)
    {
        return Agendamento::create($dados);
    }

    public function update(Agendamento $agendamento, array $dados)
    {
        return $agendamento->update($dados);
    }

    public function delete(Agendamento $agendamento)
    {
        return $agendamento->delete();
    }

    public function query()
    {
        return Agendamento::query();
    }
}
