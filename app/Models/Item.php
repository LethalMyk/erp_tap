<?php

// Model - app/Models/Item.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    use HasFactory;
    protected $fillable = ['nomeItem', 'material', 'metragem', 'especifi', 'pedido_id', 'material_disponib',]; // Adicione pedido_id

   public function pedido()
{
    return $this->belongsTo(Pedido::class);
}

}
