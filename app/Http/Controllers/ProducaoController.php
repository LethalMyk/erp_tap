<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProducaoController extends Controller
{
    public function index()
    {
        return view('producao.controle');
    }

    public function relatorios()
    {
        return view('producao.relatorios');
    }
}

