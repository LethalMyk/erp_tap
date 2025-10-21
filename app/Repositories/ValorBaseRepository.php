<?php

namespace App\Repositories;

use App\Models\ValorBase;

class ValorBaseRepository
{
    public function getByType($tipo)
    {
        return ValorBase::where('tipo', $tipo)->get();
    }

    public function getValor($tipo, $nome)
    {
        return ValorBase::where('tipo', $tipo)
                        ->where('nome', $nome)
                        ->value('valor') ?? 0;
    }

    public function getValorByNome($nome)
    {
        return ValorBase::where('nome', $nome)->value('valor') ?? 0;
    }
}
