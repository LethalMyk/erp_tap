<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemOrcamento extends Model
{
    use HasFactory;
     protected $table = 'itens_orcamento'; // <-- nome correto da tabela
    protected $fillable = [
        'orcamento_id', 'item', 'tecido', 'espumas', 'enchimentos', 'adicionais',
        'valor_base', 'valor_opcionais', 'valor_total', 'quantidade', 'subtotal'
    ];

    protected $casts = [
        'espumas' => 'array',
        'enchimentos' => 'array',
        'adicionais' => 'array'
    ];

    public function orcamento() {
        return $this->belongsTo(Orcamento::class);
    }
}
