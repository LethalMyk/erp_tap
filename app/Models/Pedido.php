<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    // Campos que podem ser preenchidos via mass assignment
    protected $fillable = [
        'cliente_id',
        'qntItens',
        'data',
        'data_retirada',
        'prazo',
        'valor',       // ✅ valor total do pedido
        'valorResta',  // opcional: valor restante a pagar
        'status',
        'obs',
        'imagem',      // caso queira gravar imagem principal
    ];

    /** RELACIONAMENTOS **/

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'pedido_id', 'id');
    }

    public function imagens()
    {
        return $this->hasMany(PedidoImagem::class);
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    public function servicos()
    {
        return $this->hasMany(Servico::class);
    }

    public function terceirizadas()
    {
        return $this->hasMany(Terceirizada::class, 'pedido_id');
    }

    /** MÉTODOS AUXILIARES **/

    // Pode adicionar método para calcular valor total a partir dos itens
    public function calcularValorTotal(): float
    {
        return $this->items->sum(function($item) {
            return ($item->quantidade ?? 1) * ($item->preco_unitario ?? 0);
        });
    }
}
