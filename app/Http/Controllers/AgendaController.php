<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function logistica()
    {
        return view('agenda.logistica');
    }

    public function orcamentos()
    {
        return view('agenda.orcamentos');
    }
}

