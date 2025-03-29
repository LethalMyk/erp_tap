<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    public function vencimentos()
    {
        return view('financeiro.vencimentos');
    }

    public function consulta()
    {
        return view('financeiro.consulta');
    }

       public function recebimento()
    {
        return view('financeiro.recebimento');
    }
}

