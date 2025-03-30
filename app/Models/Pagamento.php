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

    // Relacionamento de um pagamento com um único pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Defina se o modelo usará timestamps automaticamente
    public $timestamps = true;

    // Método que é executado sempre que um novo pagamento for criado
    protected static function boot()
    {
        parent::boot();

        static::created(function ($pagamento) {
            $pedido = $pagamento->pedido;

            // Calcular a soma dos pagamentos realizados
            $totalPago = $pedido->pagamentos->sum('valor');

            // Verificar se a soma dos pagamentos é igual ou superior ao orçamento do pedido
            if ($totalPago >= $pedido->orcamento) {
                $pedido->status = 'Pago';  // Mudar para "Pago" se o valor total dos pagamentos for suficiente
            } else {
                $pedido->status = 'Parcialmente Pago';  // Caso contrário, marca como "Parcialmente Pago"
            }

            $pedido->save();  // Salvar a atualização do status do pedido
        });
    }
}
