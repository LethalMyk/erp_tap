<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;

    protected $table = 'pagamentos';

    protected $fillable = [
        'pedido_id',
        'valor',
        'forma',
        'obs',
        'data',
        'status',
        'data_registro'
    ];

    // Relacionamento com Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }
}
