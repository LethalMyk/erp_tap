<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Item;
use App\Models\Pagamento;
use App\Models\PedidoImagem;
use App\Models\Terceirizada;
use App\Models\Agendamento;
use App\Services\ClienteService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Enums\StatusPagamento;

class PedidoService
{
    /**
     * Converte valor monetário brasileiro para float
     * Ex: "R$ 1.234,56" => 1234.56
     */
    private function formatarValor($valor)
    {
        if (is_string($valor)) {
            $valor = str_replace(['R$', ' '], '', $valor); // remove R$ e espaços
            $valor = str_replace('.', '', $valor); // remove pontos de milhar
            $valor = str_replace(',', '.', $valor); // substitui vírgula por ponto
            $valor = floatval($valor);
        }
        return $valor ?? 0;
    }

    /**
     * Cria um pedido simples
     */
    public function criarPedido(array $data)
    {
        if (!empty($data['valor'])) {
            $data['valor'] = $this->formatarValor($data['valor']);
        }

        return Pedido::create($data);
    }

    /**
     * Cria ou atualiza pedido completo (cliente, itens, pagamentos, imagens, agendamento)
     */
    public function criarPedidoCompleto(array $data)
    {
        return DB::transaction(function () use ($data) {

            // --- CLIENTE ---
            $clienteService = app(ClienteService::class);

            if (!empty($data['cliente_id'])) {
                $data['cliente']['id'] = $data['cliente_id'];
            }

            $cliente = $clienteService->criarOuAtualizarCliente($data['cliente'] ?? []);

            // --- PEDIDO ---
            $pedidoData = [
                'cliente_id'    => $cliente->id,
                'qntItens'      => $data['qntItens'] ?? 0,
                'data'          => $data['pedido']['data'] ?? now(),
                'valor'         => $this->formatarValor($data['pedido']['valor'] ?? 0),
                'valorResta'    => $this->formatarValor($data['pedido']['valor'] ?? 0),
                'status'        => 'RESTA',
                'obs'           => $data['pedido']['obs'] ?? null,
                'prazo'         => $data['pedido']['prazo'] ?? now(),
                'data_retirada' => $data['pedido']['data_retirada'] ?? null,
                'tapeceiro'     => $data['pedido']['tapeceiro'] ?? null,
                'andamento'     => 'Retirar',
            ];

            $pedido = Pedido::create($pedidoData);

            // --- AGENDAMENTO ---
            if (!empty($pedido->data_retirada)) {
                $this->criarAgendamento($pedido);
            }

            // --- ITENS E TERCEIRIZADAS ---
            foreach ($data['items'] ?? [] as $itemData) {
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

            // --- PAGAMENTOS ---
            foreach ($data['pagamentos'] ?? [] as $pagData) {
                $pagData['pedido_id'] = $pedido->id;
                $pagData['valor'] = $this->formatarValor($pagData['valor'] ?? 0);
                Pagamento::create($pagData);
            }

            // --- IMAGENS ---
            if (!empty($data['imagens'])) {
                $this->uploadImagens($pedido, $data['imagens']);
            }

            return $pedido;
        });
    }

    /**
     * Atualiza pedido e cliente
     */
    public function atualizarPedido(Pedido $pedido, array $data)
    {
        if (!empty($data['valor'])) {
            $data['valor'] = $this->formatarValor($data['valor']);
        }

        $pedido->update([
            'data'          => $data['data'] ?? $pedido->data,
            'prazo'         => $data['prazo'] ?? $pedido->prazo,
            'data_retirada' => $data['data_retirada'] ?? $pedido->data_retirada,
            'andamento'     => $data['andamento'] ?? $pedido->andamento,
            'status'        => $data['status'] ?? $pedido->status,
            'obs'           => $data['obs'] ?? $pedido->obs,
            'valor'         => $data['valor'] ?? $pedido->valor,
        ]);

        if (!empty($data['cliente'])) {
            $pedido->cliente->update($data['cliente']);
        }

        if ($pedido->data_retirada) {
            $this->criarAgendamento($pedido);
        }

        return $pedido;
    }

    // ... o resto do service permanece igual

    /**
     * Cria ou atualiza agendamento automático do pedido
     */
protected function criarAgendamento(Pedido $pedido)
{
    $cliente = $pedido->cliente;

    $agendamento = Agendamento::firstOrNew([
        'tipo'      => 'retirada',
        'pedido_id' => $pedido->id, // <-- substituído
    ]);

    $agendamento->fill([
        'qntItens'     => $pedido->qntItens ?? 0,
        'data'         => $pedido->data_retirada,
        'horario'      => '08:00',
        'nome_cliente' => $cliente->nome ?? '',
        'endereco'     => $cliente->endereco ?? '',
        'telefone'     => $cliente->telefone ?? '',
        'status'       => 'pendente',
        'obs'          => 'Agendamento automático gerado pelo pedido.',
    ]);

    $agendamento->save();
}    /**
     * Upload de imagens do pedido
     */
    public function uploadImagens(Pedido $pedido, array $imagens)
    {
        foreach ($imagens as $imagem) {
            if ($imagem->isValid()) {
                $path = $imagem->store('pedidos', 'public');
                PedidoImagem::create([
                    'pedido_id' => $pedido->id,
                    'imagem'    => $path,
                ]);
            }
        }
    }

    /**
     * Remove imagem do pedido
     */
    public function removerImagem(PedidoImagem $imagem)
    {
        Storage::disk('public')->delete($imagem->imagem);
        $imagem->delete();
    }

    /**
     * Impressão de vias
     */
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

    /**
     * Retorna pedido completo com todas relações
     */
    public function getPedidoCompleto(int $id)
    {
        return Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
    }

    /**
     * Lista pedidos com filtros
     */
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
