<?php

namespace App\Repositories;

use App\Models\Orcamento;
use App\Models\ItemOrcamento;
use Illuminate\Support\Facades\DB;

class OrcamentoRepository
{
    /**
     * Retorna todos os orçamentos, já com itens carregados
     */
    public function getAll()
    {
        return Orcamento::with('itens')->latest()->get();
    }

    /**
     * Busca um orçamento pelo ID
     */
    public function find($id)
    {
        return Orcamento::with('itens')->findOrFail($id);
    }

    /**
     * Cria um novo orçamento
     */
    public function create(array $data)
    {
        return Orcamento::create([
            'cliente_nome' => $data['cliente_nome'],
            'telefone' => $data['telefone'] ?? null,
            'email' => $data['email'] ?? null,
            'observacoes' => $data['observacoes'] ?? null,
            'status' => $data['status'] ?? 'Em Análise',
            'valor_total' => $data['valor_total'] ?? 0,
        ]);
    }

    /**
     * Adiciona um item ao orçamento
     */
    public function addItem($orcamentoId, array $itemData)
    {
        $itemData['orcamento_id'] = $orcamentoId;

        // Serializa arrays em JSON
        foreach (['espumas', 'enchimentos', 'adicionais'] as $campo) {
            if (!empty($itemData[$campo]) && is_array($itemData[$campo])) {
                $itemData[$campo] = json_encode($itemData[$campo], JSON_UNESCAPED_UNICODE);
            }
        }

        return ItemOrcamento::create($itemData);
    }

    /**
     * Atualiza o valor total do orçamento
     */
    public function updateTotal($orcamentoId, $total)
    {
        $orcamento = Orcamento::findOrFail($orcamentoId);
        $orcamento->valor_total = $total;
        $orcamento->save();
        return $orcamento;
    }

    /**
     * Atualiza o status do orçamento
     */
    public function updateStatus($orcamentoId, $status)
    {
        $orcamento = Orcamento::findOrFail($orcamentoId);
        $orcamento->status = $status;
        $orcamento->save();
        return $orcamento;
    }

    /**
     * Remove um item do orçamento
     */
    public function removeItem($itemId)
    {
        $item = ItemOrcamento::findOrFail($itemId);
        return $item->delete();
    }

    /**
     * Retorna todos os itens de um orçamento
     */
    public function getItens($orcamentoId)
    {
        return ItemOrcamento::where('orcamento_id', $orcamentoId)->get();
    }

    /**
     * Converter orçamento para pedido
     * Este método pode apenas retornar os dados prontos
     */
    public function prepareForPedido($orcamentoId)
    {
        $orcamento = $this->find($orcamentoId);
        $itens = $orcamento->itens->map(function($item) {
            return [
                'descricao' => $item->item,
                'tecido' => $item->tecido,
                'quantidade' => $item->quantidade,
                'valor_unitario' => $item->valor_total,
                'subtotal' => $item->subtotal,
                'espumas' => $item->espumas,
                'enchimentos' => $item->enchimentos,
                'adicionais' => $item->adicionais,
            ];
        });
        return [
            'orcamento' => $orcamento,
            'itens' => $itens
        ];
    }
}
