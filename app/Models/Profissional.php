<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profissional extends Model
{
    use HasFactory;
    protected $table = 'profissional'; // Força o nome correto da tabela
    protected $fillable = ['nome', 'cargo'];
    public function servicos()
{
    return $this->hasMany(Servico::class);
}
}
