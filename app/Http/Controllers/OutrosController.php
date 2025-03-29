<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OutrosController extends Controller
{
    public function imper()
    {
        return view('outros.imper');
    }

    public function pintura()
    {
        return view('outros.pintura');
    }

       public function fabric()
    {
        return view('outros.fabric');
    }
}

