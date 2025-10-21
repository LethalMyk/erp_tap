<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValorBase extends Model
{
    use HasFactory;

     protected $table = 'valores_base'; // <-- nome correto da tabela
    protected $fillable = ['tipo', 'nome', 'valor', 'unidade'];
}
