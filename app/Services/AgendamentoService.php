<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Item;
use App\Models\Agendamento;
use App\Repositories\AgendamentoRepository;

class AgendamentoService
{
    protected $repository;

    public function __construct(AgendamentoRepository $repository)
    {
        $this->repository = $repository;
    }

    // Lista todos os agendamentos
    public function listarTodos()
    {
        return $this->repository->all();
    }

    // Busca um agendamento pelo ID
    public function buscarPorId($id)
    {
        return $this->repository->find($id);
    }

    // Cria um agendamento
    public function criar(array $dados)
    {
        // Se houver cliente_id, preenche os dados do cliente
        if (!empty($dados['cliente_id'])) {
            $cliente = Cliente::find($dados['cliente_id']);
            if ($cliente) {
                $dados['nome_cliente'] = $cliente->nome;
                $dados['endereco'] = $cliente->endereco;
                $dados['telefone'] = $cliente->telefone;
            }
        }

        // Preenche campos padrão se não vierem no array
        $dados['tipo'] = $dados['tipo'] ?? 'retirada';
        $dados['data'] = $dados['data'] ?? now()->toDateString();
        $dados['horario'] = $dados['horario'] ?? '09:00';
        $dados['itens'] = $dados['itens'] ?? '';
        $dados['observacao'] = $dados['observacao'] ?? '';
        $dados['status'] = $dados['status'] ?? 'pendente';

        return $this->repository->create($dados);
    }

    // Atualiza um agendamento
    public function atualizar(Agendamento $agendamento, array $dados)
    {
        if (!empty($dados['cliente_id'])) {
            $cliente = Cliente::find($dados['cliente_id']);
            if ($cliente) {
                $dados['nome_cliente'] = $cliente->nome;
                $dados['endereco'] = $cliente->endereco;
                $dados['telefone'] = $cliente->telefone;
            }
        }

        return $this->repository->update($agendamento, $dados);
    }

    // Deleta um agendamento
    public function deletar(Agendamento $agendamento)
    {
        return $this->repository->delete($agendamento);
    }

    // Retorna eventos para o calendário
    public function eventos($tipo = null, $status = null)
    {
        $query = $this->repository->query();

        if ($tipo) $query->where('tipo', $tipo);
        if ($status) $query->where('status', $status);

        return $query->get()->map(function ($ag) {
            $coresPorTipo = [
                'entrega' => '#007bff',
                'retirada' => '#ffc107',
                'orcamento' => '#6f42c1',
                'assistencia' => '#e25822',
            ];
            return [
                'id' => $ag->id,
                'title' => $ag->nome_cliente,
                'start' => $ag->data . 'T' . $ag->horario,
                'color' => $coresPorTipo[$ag->tipo] ?? '#007bff',
                'status' => $ag->status ?? 'pendente',
                'tipo' => $ag->tipo,
                'endereco' => $ag->endereco,
                'telefone' => $ag->telefone ?? 'Não informado',
                'itens' => $ag->itens ?? '',
                'observacao' => $ag->observacao ?? '',
            ];
        });
    }

    // Retorna itens e observações do cliente
    public function getItensCliente($clienteId)
    {
        $itens = Item::with('pedido')->whereHas('pedido', function ($query) use ($clienteId) {
            $query->where('cliente_id', $clienteId);
        })->get();

        return [
            'itens' => $itens->pluck('nomeItem')->filter()->implode(' - '),
            'observacao' => $itens->pluck('pedido.obs_retirada')->filter()->unique()->implode(' | '),
        ];
    }
}
