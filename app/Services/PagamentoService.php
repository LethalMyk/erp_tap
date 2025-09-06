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

    public function criar(array $dados)
    {
        $pedido = Pedido::findOrFail($dados['pedido_id']);

        $dados['valor'] = $this->formatarValor($dados['valor'] ?? 0);

        $forma = strtoupper(trim($dados['forma']));
        $formasEmAberto = ['BOLETO', 'CHEQUE', 'OUTROS', 'NA ENTREGA', 'A PRAZO'];

        $status = in_array($forma, $formasEmAberto) ? 'EM ABERTO' : 'PAGAMENTO REGISTRADO';
        $dados['status'] = $status;
        $dados['data'] = $dados['data'] ?? now();

        $pagamento = $this->repository->create($dados);

        if ($status === 'PAGAMENTO REGISTRADO') {
            $this->atualizarStatusPedido($pedido);
        }

        return $pagamento;
    }

    public function atualizar(Pagamento $pagamento, array $dados)
    {
        $pedido = $pagamento->pedido;

        if (isset($dados['valor'])) {
            $dados['valor'] = $this->formatarValor($dados['valor']);
        }

        if (isset($dados['forma'])) {
            $forma = strtoupper(trim($dados['forma']));
            $formasEmAberto = ['BOLETO', 'CHEQUE', 'OUTROS', 'NA ENTREGA', 'A PRAZO'];
            $dados['status'] = in_array($forma, $formasEmAberto) ? 'EM ABERTO' : 'PAGAMENTO REGISTRADO';
        }

        $this->repository->update($pagamento, $dados);

        $pagamento->refresh();
        $this->atualizarStatusPedido($pedido);

        return $pagamento;
    }

    public function deletar(Pagamento $pagamento)
    {
        $pedido = $pagamento->pedido;
        $this->repository->delete($pagamento);
        $this->atualizarStatusPedido($pedido);
    }

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
