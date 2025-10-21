<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    use HasFactory;

    protected $fillable = ['cliente_nome', 'telefone', 'email', 'status', 'valor_total', 'observacoes'];

    public function itens() {
        return $this->hasMany(ItemOrcamento::class);
    }
}
