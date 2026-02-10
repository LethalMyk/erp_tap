<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Item;
use App\Models\Pagamento;
use App\Models\PedidoImagem;
use App\Models\Terceirizada;
use App\Models\Agendamento;
use App\Services\ClienteService;
use App\Services\ListaCompraService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Enums\StatusPagamento;

class PedidoService
{
    protected $clienteService;
    protected $listaCompraService;

    public function __construct(ClienteService $clienteService, ListaCompraService $listaCompraService)
    {
        $this->clienteService = $clienteService;
        $this->listaCompraService = $listaCompraService;
    }

    /**
     * Converte valor monetário brasileiro para float
     * Ex: "R$ 1.234,56" => 1234.56
     */
    private function formatarValor($valor)
    {
        if (is_string($valor)) {
            $valor = str_replace(['R$', ' '], '', $valor);
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
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
            if (!empty($data['cliente_id'])) {
                $data['cliente']['id'] = $data['cliente_id'];
            }
            $cliente = $this->clienteService->criarOuAtualizarCliente($data['cliente'] ?? []);

            // --- VALORES ---
            $valorPedido = $this->formatarValor($data['pedido']['valor'] ?? $data['valor'] ?? 0);

            // --- PEDIDO ---
            $periodo = $data['pedido']['periodo_retirada'] ?? '';
            $pedidoData = [
                'cliente_id'       => $cliente->id,
                'qntItens'         => $data['qntItens'] ?? 0,
                'data'             => $data['pedido']['data'] ?? now(),
                'valor'            => $valorPedido,
                'valorResta'       => $valorPedido,
                'status'           => 'RESTA',
                'obs'              => $data['pedido']['obs'] ?? null,
                'prazo'            => $data['pedido']['prazo'] ?? now(),
'data_retirada' => $data['pedido']['data_retirada']
    ?? $data['data_retirada']
    ?? null,
                'periodo_retirada' => in_array($periodo, ['Manhã', 'Tarde']) ? $periodo : 'Combinar',
                'tapeceiro'        => $data['pedido']['tapeceiro'] ?? null,
                'andamento'        => 'Retirar',
            ];

            $pedido = Pedido::create($pedidoData);

            // --- AGENDAMENTO ---
            if (!empty($pedido->data_retirada)) {
                $this->criarAgendamento($pedido, $cliente);
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

                // --- CRIA LISTA DE COMPRAS ---
                $this->listaCompraService->criar([
                    'material'   => $item->material,
                    'metragem'   => $item->metragem,
                    'fornecedor' => $item->fornecedor ?? null,
                    'situacao'   => 'pendente',
                    'pedido_id'  => $pedido->id
                ]);
            }

            // --- PAGAMENTOS ---
            $formasParaRegistrar = ['PIX','DEBITO','DINHEIRO','CREDITO À VISTA','CREDITO PARCELADO'];

            foreach ($data['pagamentos'] ?? [] as $pagData) {
                $pagData['pedido_id'] = $pedido->id;
                $pagData['valor'] = $this->formatarValor($pagData['valor'] ?? 0);

                if (!empty($pagData['status'])) {
                    $status = $pagData['status'];
                } else {
                    // Se a forma de pagamento estiver na lista de registrar, marca como PAGO
                    if (!empty($pagData['forma']) && in_array($pagData['forma'], $formasParaRegistrar)) {
                        $status = StatusPagamento::PAGO->value;
                    } else {
                        $status = StatusPagamento::PENDENTE->value;
                    }
                }

                $pagData['status'] = $status;

                Pagamento::create($pagData);
            }

            // --- IMAGENS ---
            if (!empty($data['imagens'])) {
                $this->uploadImagens($pedido, $data['imagens']);
            }

            // --- ATUALIZA STATUS DO PEDIDO ---
$totalPago = Pagamento::where('pedido_id', $pedido->id)
    ->where('status', 'PAGAMENTO REGISTRADO')
    ->sum('valor');

$novoValorResta = max(0, $pedido->valor - $totalPago);

$pedido->update([
    'valorResta' => $novoValorResta,
    'status' => $novoValorResta == 0 ? 'PAGO' : 'RESTA'
]);
            return $pedido;
        });
    }

    /**
     * Atualiza pedido e cliente
     */
    public function atualizarPedido(Pedido $pedido, array $data)
    {
        $valorPedido = $this->formatarValor($data['valor'] ?? $pedido->valor);

        $pedido->update([
            'data'             => $data['data'] ?? $pedido->data,
            'prazo'            => $data['prazo'] ?? $pedido->prazo,
            'data_retirada'    => $data['data_retirada'] ?? $pedido->data_retirada,
            'andamento'        => $data['andamento'] ?? $pedido->andamento,
            'status'           => $data['status'] ?? $pedido->status,
            'obs'              => $data['obs'] ?? $pedido->obs,
            'valor'            => $valorPedido,
            'periodo_retirada' => in_array($data['periodo_retirada'] ?? '', ['Manhã', 'Tarde']) ? $data['periodo_retirada'] : $pedido->periodo_retirada,
        ]);

        if (!empty($data['cliente'])) {
            $pedido->cliente->update($data['cliente']);
        }

        if ($pedido->data_retirada) {
            $this->criarAgendamento($pedido, $pedido->cliente);
        }

        return $pedido;
    }

    /**
     * Cria ou atualiza agendamento automático do pedido
     */
    protected function criarAgendamento(Pedido $pedido, $cliente)
    {
        $agendamento = Agendamento::firstOrNew([
            'tipo'      => 'retirada',
            'pedido_id' => $pedido->id,
        ]);

        $agendamento->fill([
            'qntItens'     => $pedido->qntItens ?? 0,
            'data'         => $pedido->data_retirada,
            'horario'      => '08:00',
            'nome_cliente' => $cliente->nome ?? '',
            'endereco'     => $this->clienteService->getEnderecoCompleto($cliente),
            'telefone'     => $cliente->telefone ?? '',
            'status'       => 'pendente',
            'obs'          => 'Agendamento automático gerado pelo pedido.',
        ]);

        $agendamento->save();
    }

    /**
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
        $query = Pedido::query();
$query->with(['cliente', 'profissional', 'imagens', 'items', 'agendamento']);

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['tapeceiro'])) {
            $query->where('tapeceiro', $filters['tapeceiro']);
        }

        if (!empty($filters['nome'])) {
            $query->whereHas('cliente', fn($q) => $q->where('nome', 'like', '%' . $filters['nome'] . '%'));
        }

        if (!empty($filters['endereco'])) {
            $query->whereHas('cliente', fn($q) => $q->where('endereco', 'like', '%' . $filters['endereco'] . '%'));
        }

        if (!empty($filters['telefone'])) {
            $query->whereHas('cliente', fn($q) => $q->where('telefone', 'like', '%' . $filters['telefone'] . '%'));
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['ano']) && is_array($filters['ano'])) {
            $query->whereIn(\DB::raw('YEAR(created_at)'), $filters['ano']);
        }

        if (!empty($filters['mes']) && is_array($filters['mes'])) {
            $query->whereIn(\DB::raw('MONTH(created_at)'), $filters['mes']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
