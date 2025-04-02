<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\StatusPagamento;

class Terceirizada extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipoServico',
        'obs',
        'item_id',
        'pedido_id',
        'andamento',
        'valor',
        'statusPg'
    ];

    protected $casts = [
        'valor' => 'decimal:2', // Armazena valor com duas casas decimais
        'statusPg' => StatusPagamento::class, // Usa Enum para status de pagamento
    ];

    protected $attributes = [
        'andamento' => 'em espera',
        'statusPg' => 'em aberto',
        'valor' => 0.00,
    ];

    // Relacionamento com Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Relacionamento com Item
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'id');
    }

    // Relacionamento indireto com Cliente através do Pedido
    public function cliente()
    {
        return $this->hasOneThrough(
            Cliente::class,  // Modelo Cliente
            Pedido::class,   // Modelo intermediário Pedido
            'id',            // Chave primária em Pedido
            'id',            // Chave primária em Cliente
            'pedido_id',     // Chave estrangeira em Terceirizada (ligando ao Pedido)
            'cliente_id'     // Chave estrangeira em Pedido (ligando ao Cliente)
        );
    }
}