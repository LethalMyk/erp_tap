<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;

    // Defina os campos que podem ser preenchidos
    protected $fillable = [
        'pedido_id',
        'valor',
        'forma',
        'descricao',
    ];

    public function pedido()
{
    return $this->belongsTo(Pedido::class, 'pedido_id');
}

    // Defina se o modelo usará timestamps automaticamente
    public $timestamps = true;
}
