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

    public function listarTodos()
    {
        return $this->repository->all();
    }

    public function buscarPorId($id)
    {
        return $this->repository->find($id);
    }

    public function criar(array $dados)
    {
        $pedido = Pedido::findOrFail($dados['pedido_id']);

        $forma = strtoupper(trim($dados['forma']));
        $formasEmAberto = ['BOLETO', 'CHEQUE', 'OUTROS', 'NA ENTREGA', 'A PRAZO'];

        // Define status do pagamento
        $status = in_array($forma, $formasEmAberto) ? 'EM ABERTO' : 'PAGAMENTO REGISTRADO';
        $dados['status'] = $status;
        $dados['data'] = $dados['data'] ?? now();

        $pagamento = $this->repository->create($dados);

        // Atualiza pedido apenas se o pagamento estiver registrado
        if ($status === 'PAGAMENTO REGISTRADO') {
            $this->atualizarStatusPedido($pedido);
        }

        return $pagamento;
    }

    public function atualizar(Pagamento $pagamento, array $dados)
    {
        $pedido = $pagamento->pedido;

        if (isset($dados['forma'])) {
            $forma = strtoupper(trim($dados['forma']));
            $formasEmAberto = ['BOLETO', 'CHEQUE', 'OUTROS', 'NA ENTREGA', 'A PRAZO'];
            $dados['status'] = in_array($forma, $formasEmAberto) ? 'EM ABERTO' : 'PAGAMENTO REGISTRADO';
        }

        $this->repository->update($pagamento, $dados);

        // Atualiza pedido com base no status atualizado do pagamento
        $pagamento->refresh(); // garante que temos o status atualizado
        if ($pagamento->status === 'PAGAMENTO REGISTRADO') {
            $this->atualizarStatusPedido($pedido);
        } else {
            // Se o pagamento passou a estar em aberto, também recalcula valorResta
            $this->atualizarStatusPedido($pedido);
        }

        return $pagamento;
    }

    public function deletar(Pagamento $pagamento)
    {
        $pedido = $pagamento->pedido;
        $this->repository->delete($pagamento);

        // Atualiza valor restante após exclusão
        $this->atualizarStatusPedido($pedido);
    }

    public function registrar(Pagamento $pagamento, $obs = null)
    {
        // Só registra pagamentos que estavam em aberto
        if ($pagamento->status === 'EM ABERTO') {
            $pagamento->status = 'PAGAMENTO REGISTRADO';
            $pagamento->data_registro = now();
            $pagamento->obs = $obs ?? $pagamento->obs;
            $pagamento->save();

            $this->atualizarStatusPedido($pagamento->pedido);
        }

        return $pagamento;
    }

    private function atualizarStatusPedido(Pedido $pedido)
    {
        // Soma apenas pagamentos registrados
        $totalPago = $this->repository->sumPagamentosRegistrados($pedido->id);
        $novoValorResta = max(0, $pedido->valor - $totalPago);
        $novoStatus = ($novoValorResta == 0) ? 'PAGO' : 'RESTA';

        $pedido->update([
            'valorResta' => $novoValorResta,
            'status' => $novoStatus,
        ]);
    }
}
