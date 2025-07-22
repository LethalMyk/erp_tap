<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Despesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'descricao',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status',
        'categoria',
        'forma_pagamento',
        'chave_pagamento',
        'comprovante',
        'observacao',
        'created_by',
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
