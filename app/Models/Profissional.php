<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profissional extends Model
{
    use HasFactory;
    protected $table = 'profissional'; // Força o nome correto da tabela
    protected $fillable = ['id', 'nome', 'cargo'];
}
