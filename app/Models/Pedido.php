<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;  // Importando o modelo Client

class Pedido extends Model
{
    use HasFactory;

    protected $primaryKey = 'pedido_id';

    protected $fillable = [
        'client_id', 
        'data', 
        'orcamento', 
        'status', 
        'prazo', 
        'data_retirada', 
        'imagens',  // Adicionando campo imagem ao fillable
        'obs'
    ];

    // Relacionamento com Pagamentos
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'pedido_id');
    }

    // Relacionamento com Client (não Cliente, porque o modelo é Client)
    public function client()  // Nome do método deve ser 'client' para manter consistência
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

public function atualizarStatusPagamento($status)
{
    $this->status = $status;  // Atualiza o status
    $this->save();  // Salva a alteração no banco de dados
}

public function imagens()
{
    return $this->hasMany(ImagemPedido::class, 'pedido_id');
}
}

