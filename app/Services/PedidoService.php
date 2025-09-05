<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Item;
use App\Models\Pagamento;
use App\Models\PedidoImagem;
use App\Models\Terceirizada;
use App\Models\Agendamento;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Enums\StatusPagamento;

class PedidoService
{
    public function criarPedido(array $data)
    {
        return Pedido::create($data);
    }

    public function criarPedidoCompleto(array $data)
    {
        return DB::transaction(function () use ($data) {

            $cliente = Cliente::updateOrCreate(
                ['id' => $data['cliente_id'] ?? null],
                $data['cliente'] ?? []
            );

            $pedido = Pedido::create([
                'cliente_id'   => $cliente->id,
                'qntItens'     => $data['qntItens'] ?? 0,
                'data'         => $data['pedido']['data'] ?? now(),
                'valor'        => $data['pedido']['valor'] ?? 0,
                'valorResta'   => $data['pedido']['valor'] ?? 0,
                'status'       => 'RESTA',
                'obs'          => $data['pedido']['obs'] ?? null,
                'prazo'        => $data['pedido']['prazo'] ?? now(),
                'data_retirada'=> $data['pedido']['data_retirada'] ?? null,
                'tapeceiro'    => $data['pedido']['tapeceiro'] ?? null,
                'andamento'    => 'Retirar',
            ]);

            // Cria agendamento apenas se houver data de retirada
            if ($pedido->data_retirada) {
                $this->criarAgendamento($pedido);
            }

            // Itens e terceirizadas
            $items = $data['items'] ?? [];
            foreach ($items as $itemData) {
                $terceirizadas = $itemData['terceirizadas'] ?? [];
                unset($itemData['terceirizadas']);
                $itemData['pedido_id'] = $pedido->id;
                $item = Item::create($itemData);

                foreach ($terceirizadas as $t) {
                    $t['pedido_id'] = $pedido->id;
                    $t['item_id']   = $item->id;
                    $t['statusPg']  = $t['statusPg'] ?? StatusPagamento::PENDENTE->value;
                    Terceirizada::create($t);
                }
            }

            // Pagamentos
            $pagamentos = $data['pagamentos'] ?? [];
            foreach ($pagamentos as $pagData) {
                $pagData['pedido_id'] = $pedido->id;
                Pagamento::create($pagData);
            }

            // Upload de imagens
            if (!empty($data['imagens'])) {
                $this->uploadImagens($pedido, $data['imagens']);
            }

            return $pedido;
        });
    }

    public function atualizarPedido(Pedido $pedido, array $data)
    {
        $pedido->update([
            'data'         => $data['data'] ?? $pedido->data,
            'prazo'        => $data['prazo'] ?? $pedido->prazo,
            'data_retirada'=> $data['data_retirada'] ?? $pedido->data_retirada,
            'andamento'    => $data['andamento'] ?? $pedido->andamento,
            'status'       => $data['status'] ?? $pedido->status,
            'obs'          => $data['obs'] ?? $pedido->obs,
        ]);

        if (!empty($data['cliente'])) {
            $pedido->cliente->update($data['cliente']);
        }

        // Cria ou atualiza agendamento apenas se houver data de retirada
        if ($pedido->data_retirada) {
            $this->criarAgendamento($pedido);
        }

        return $pedido;
    }

    protected function criarAgendamento(Pedido $pedido)
    {
        $cliente = $pedido->cliente;

        // Cria novo agendamento ou atualiza existente
        $agendamento = Agendamento::firstOrNew([
            'tipo'  => 'retirada',
            'items' => 'Pedido #' . $pedido->id,
        ]);

        $agendamento->fill([
            'qntItens'    => $pedido->qntItens ?? 0,
            'data'        => $pedido->data_retirada,
            'horario'     => '08:00',
            'nome_cliente'=> $cliente->nome ?? '',
            'endereco'    => $cliente->endereco ?? '',
            'telefone'    => $cliente->telefone ?? '',
            'status'      => 'pendente',
            'obs'         => 'Agendamento automático gerado pelo pedido.',
        ]);

        $agendamento->save();
    }

    public function uploadImagens(Pedido $pedido, array $imagens)
    {
        foreach ($imagens as $imagem) {
            if ($imagem->isValid()) {
                $path = $imagem->store('pedidos', 'public');
                PedidoImagem::create([
                    'pedido_id' => $pedido->id,
                    'imagem'    => $path
                ]);
            }
        }
    }

    public function removerImagem(PedidoImagem $imagem)
    {
        Storage::disk('public')->delete($imagem->imagem);
        $imagem->delete();
    }

    public function gerarImpressaoViaTap($pedido)
{
    return view('pedidos.vias.imprimirviatap', compact('pedido'));
}

public function gerarImpressaoViaRetirada($pedido)
{
    return view('pedidos.vias.imprimirviaretirada', compact('pedido'));
}

public function gerarImpressaoViaCompleta($pedido)
{
    return view('pedidos.vias.imprimirviacompleta', compact('pedido'));
}

    public function getPedidoCompleto(int $id)
    {
        return Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
    }

    public function listarPedidos(array $filters = [])
    {
        $query = Pedido::with(['cliente', 'items', 'pagamentos', 'imagens']);

        if (!empty($filters['cliente_id'])) {
            $query->where('cliente_id', $filters['cliente_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('id', 'desc')->get();
    }
}
