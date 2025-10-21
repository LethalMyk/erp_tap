<?php

namespace App\Services;

use App\Repositories\OrcamentoRepository;
use App\Repositories\ValorBaseRepository;
use App\Models\Cliente;


class OrcamentoService
{
    protected $orcamentoRepository;
    protected $valorBaseRepository;

    public function __construct(
        OrcamentoRepository $orcamentoRepository,
        ValorBaseRepository $valorBaseRepository
    ) {
        $this->orcamentoRepository = $orcamentoRepository;
        $this->valorBaseRepository = $valorBaseRepository;
    }

    
    public function listarOrcamentos()
    {
        return $this->orcamentoRepository->getAll();
    }

    public function criarOrcamento(array $dados)
    {
        $orcamento = $this->orcamentoRepository->create([
            'cliente_nome' => $dados['cliente_nome'],
            'telefone' => $dados['telefone'] ?? null,
            'email' => $dados['email'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null,
        ]);

        $totalGeral = 0;

        foreach ($dados['itens'] as $item) {
            $valorBase = $this->valorBaseRepository->getValor('item', $item['item']);
            $valorTecido = $this->valorBaseRepository->getValor('tecido', $item['tecido'] ?? '');

            $valorOpcionais = 0;
            foreach (['espumas', 'enchimentos', 'adicionais'] as $tipo) {
                if (!empty($item[$tipo])) {
                    foreach ($item[$tipo] as $nome) {
                        $valorOpcionais += $this->valorBaseRepository->getValorByNome($nome);
                    }
                }
            }

            $valorTotalItem = $valorBase + $valorTecido + $valorOpcionais;
            $quantidade = $item['quantidade'] ?? 1;
            $subtotal = $valorTotalItem * $quantidade;
            $totalGeral += $subtotal;

            $this->orcamentoRepository->addItem($orcamento->id, [
                'item' => $item['item'],
                'tecido' => $item['tecido'] ?? null,
                'espumas' => $item['espumas'] ?? [],
                'enchimentos' => $item['enchimentos'] ?? [],
                'adicionais' => $item['adicionais'] ?? [],
                'valores_base' => $valorBase,
                'valor_opcionais' => $valorOpcionais,
                'valor_total' => $valorTotalItem,
                'quantidade' => $quantidade,
                'subtotal' => $subtotal,
            ]);
        }

        $this->orcamentoRepository->updateTotal($orcamento->id, $totalGeral);

        return $orcamento;
    }

    public function carregarOpcoes()
    {
        return [
            'itens' => $this->valorBaseRepository->getByType('item'),
            'tecidos' => $this->valorBaseRepository->getByType('tecido'),
            'espumas' => $this->valorBaseRepository->getByType('espuma'),
            'enchimentos' => $this->valorBaseRepository->getByType('enchimento'),
            'adicionais' => $this->valorBaseRepository->getByType('adicional'),
        ];
    }
      public function listarClientes()
    {
        return Cliente::all();
    }
}
