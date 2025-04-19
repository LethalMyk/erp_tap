<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profissional extends Model
{
    use HasFactory;
    protected $table = 'profissional'; // Força o nome correto da tabela
<<<<<<< HEAD
    protected $fillable = ['nome', 'cargo'];
    public function servicos()
{
    return $this->hasMany(Servico::class);
}
=======
    protected $fillable = ['id', 'nome', 'cargo'];
>>>>>>> 584ccf135e8a120770c8fde68cc414c3886ccea4
}
