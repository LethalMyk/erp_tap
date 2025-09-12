<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Pagamento;
use App\Repositories\PagamentoRepository;

class PagamentoService
{
    protected $repository;

    public function __construct(PagamentoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Converte valor monetário brasileiro para float
     * Ex: "R$ 1.234,56" => 1234.56
     */
    private function formatarValor($valor)
    {
        if (is_string($valor)) {
            $valor = str_replace(['R$', ' '], '', $valor); // remove R$ e espaços
            $valor = str_replace('.', '', $valor);         // remove separador de milhar
            $valor = str_replace(',', '.', $valor);        // substitui vírgula por ponto
            $valor = floatval($valor);
        }
        return $valor ?? 0;
    }

    public function listarTodos()
    {
        return $this->repository->all();
    }

    public function buscarPorId($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Cria um novo pagamento sempre em aberto
     */
    public function criar(array $dados)
    {
        $pedido = Pedido::findOrFail($dados['pedido_id']);

        $dados['valor'] = $this->formatarValor($dados['valor'] ?? 0);
        $dados['status'] = 'EM ABERTO';
        $dados['data'] = $dados['data'] ?? now();

        $pagamento = $this->repository->create($dados);

        // não atualiza status do pedido ainda (só quando registrar)
        return $pagamento;
    }

    /**
     * Atualiza um pagamento existente
     */
    public function atualizar(Pagamento $pagamento, array $dados)
    {
        $pedido = $pagamento->pedido;

        if (isset($dados['valor'])) {
            $dados['valor'] = $this->formatarValor($dados['valor']);
        }

        // não alterar status aqui, só valor/forma/obs
        $this->repository->update($pagamento, $dados);

        $pagamento->refresh();
        $this->atualizarStatusPedido($pedido);

        return $pagamento;
    }

    /**
     * Deleta um pagamento
     */
    public function deletar(Pagamento $pagamento)
    {
        $pedido = $pagamento->pedido;
        $this->repository->delete($pagamento);
        $this->atualizarStatusPedido($pedido);
    }

    /**
     * Registra efetivamente o pagamento (confirma)
     */
    public function registrar(Pagamento $pagamento, $obs = null)
    {
        if ($pagamento->status === 'EM ABERTO') {
            $pagamento->status = 'PAGAMENTO REGISTRADO';
            $pagamento->data_registro = now();
            $pagamento->obs = $obs ?? $pagamento->obs;
            $pagamento->save();

            $this->atualizarStatusPedido($pagamento->pedido);
        }

        return $pagamento;
    }

    /**
     * Atualiza o status do pedido baseado nos pagamentos já registrados
     */
    private function atualizarStatusPedido(Pedido $pedido)
    {
        $totalPago = $this->repository->sumPagamentosRegistrados($pedido->id);
        $novoValorResta = max(0, $pedido->valor - $totalPago);
        $novoStatus = ($novoValorResta == 0) ? 'PAGO' : 'RESTA';

        $pedido->update([
            'valorResta' => $novoValorResta,
            'status' => $novoStatus,
        ]);
    }
}
