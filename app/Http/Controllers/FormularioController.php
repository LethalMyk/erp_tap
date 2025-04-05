<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\Item;
use App\Models\Pagamento;
use App\Models\PedidoImagem;
use App\Models\Terceirizada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FormularioController extends Controller
{
    public function store(Request $request)
    {
        // 1️⃣ Criar Cliente
        $cliente = Cliente::create([
            'nome' => $request->input('cliente.nome'),
            'telefone' => $request->input('cliente.telefone'),
            'endereco' => $request->input('cliente.endereco'),
            'cpf' => $request->input('cliente.cpf'),
            'email' => $request->input('cliente.email'),
        ]);

        // 2️⃣ Criar Pedido
        $pedido = Pedido::create([
            'cliente_id' => $cliente->id,
            'data' => $request->input('pedido.data'),
            'data_retirada' => $request->input('pedido.data_retirada'),
            'prazo' => $request->input('pedido.prazo'),
            'valor' => $request->input('pedido.valor'),
            'valor_resta' => $request->input('pedido.valor_resta'),
        ]);

        // 3️⃣ Criar Itens e Serviços Terceirizados
        foreach ($request->input('items', []) as $itemData) {
            $item = Item::create([
                'pedido_id' => $pedido->id,
                'nomeItem' => $itemData['nomeItem'],
                'material' => $itemData['material'],
                'metragem' => $itemData['metragem'],
                'especifi' => $itemData['especifi'] ?? null,
                
            ]);

            // Criar Serviços Terceirizados
            if (!empty($itemData['terceirizadas'])) {
                foreach ($itemData['terceirizadas'] as $terceirizadaData) {
                    Terceirizada::create([
                        'item_id' => $item->id,
                     'pedido_id' => $pedido->id, // Adicione essa linha caso seja necessário
                        'tipo' => $terceirizadaData['tipo'],
                        'obs' => $terceirizadaData['obs'] ?? null,
                        'statusPg' => 'Pendente', // Ou 'Pago' ou 'Parcial', conforme necessário

                    ]);
                }
            }
        }

        // 4️⃣ Criar Pagamentos
        foreach ($request->input('pagamentos', []) as $pagamentoData) {
            Pagamento::create([
                'pedido_id' => $pedido->id,
                'valor' => $pagamentoData['valor'],
                'forma' => $pagamentoData['forma'],
                'obs' => $pagamentoData['obs'],
            ]);
        }

        // 5️⃣ Upload de Imagens
        if ($request->hasFile('imagens')) {
            foreach ($request->file('imagens') as $imagem) {
                if ($imagem->isValid()) {
                    $path = $imagem->store('pedidos', 'public');
                    PedidoImagem::create([
                        'pedido_id' => $pedido->id,
                        'imagem' => $path
                    ]);
                }
            }
        }

        return redirect()->route('formulario.index')->with('success', 'Formulário salvo com sucesso!');
    }

    public function index()
    {
        return view('formulario');
    }
    public function visualizar($id)
{
    $pedido = Pedido::with([
        'cliente',
        'items.terceirizadas',
        'pagamentos',
        'imagens'
    ])->findOrFail($id);

    return view('viewpedido', compact('pedido'));
}

}
