<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class ProducaoController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->input('id');
        $tapeceiro = $request->input('tapeceiro');
        $etapa = $request->input('etapa');
        $dataInicio = $request->input('data_inicio');
        $dataTermino = $request->input('data_termino');
        $clienteNome = $request->input('cliente_nome');
        $endereco = $request->input('endereco');
        $telefone = $request->input('telefone');
        $andamento = $request->input('andamento');
        $mes = $request->input('mes');
        $data = $request->input('data');

        // Parâmetros para ordenação
        $sortField = $request->input('sort_field');
        $sortDirection = $request->input('sort_direction', 'desc'); // padrão desc

        // Ordenação customizada por lista fixa de tapeceiros (ex: "Samuel,Paulo,Andre,Jose,Distribuir")
        $customOrder = $request->input('custom_order');

        // Campos permitidos para ordenar
        $allowedSortFields = ['id', 'data', 'andamento', 'tapeceiro', 'prazo', 'cliente_nome'];

$query = Pedido::with(['cliente', 'servicos.profissional', 'items'])
            ->when($data, fn($q) => $q->whereDate('data', $data))
            ->when($id, fn($q) => $q->where('id', $id))
            // Modificado aqui: aceita múltiplos tapeceiros separados por vírgula
            ->when($tapeceiro, function ($q) use ($tapeceiro) {
                $names = array_map('trim', explode(',', $tapeceiro));
                // Pode usar whereIn para filtro exato:
                return $q->whereIn('tapeceiro', $names);
                // Se quiser filtro parcial (LIKE) por cada nome, descomente abaixo e comente o whereIn:
                /*
                return $q->where(function ($q2) use ($names) {
                    foreach ($names as $name) {
                        $q2->orWhere('tapeceiro', 'like', '%' . $name . '%');
                    }
                });
                */
            })
            ->when($etapa, fn($q) => $q->whereHas('servicos', fn($q2) => $q2->where('tipo_servico', $etapa)))
            ->when($dataInicio, fn($q) => $q->whereHas('servicos', fn($q2) => $q2->whereDate('data_inicio', '>=', $dataInicio)))
            ->when($dataTermino, fn($q) => $q->whereHas('servicos', fn($q2) => $q2->whereDate('data_termino', '<=', $dataTermino)))
            ->when($clienteNome, fn($q) => $q->whereHas('cliente', fn($q2) => $q2->where('nome', 'like', '%' . $clienteNome . '%')))
            ->when($endereco, fn($q) => $q->whereHas('cliente', fn($q2) => $q2->where('endereco', 'like', '%' . $endereco . '%')))
            ->when($telefone, fn($q) => $q->whereHas('cliente', fn($q2) => $q2->where('telefone', 'like', '%' . $telefone . '%')))
            ->when($andamento, fn($q) => $q->whereIn('andamento', (array) $andamento))
            ->when($mes, fn($q) => $q->whereHas('servicos', fn($q2) => $q2->whereMonth('data_termino', '=', $mes)));

        // Aplicar ordenação
        if ($customOrder) {
            $names = array_map('trim', explode(',', $customOrder));
            // Sanitiza nomes para evitar SQL injection
            $names = array_map(fn($name) => addslashes($name), $names);
            $fieldList = "'" . implode("','", $names) . "'";
            $query->orderByRaw("FIELD(tapeceiro, $fieldList)");
        } elseif (in_array($sortField, $allowedSortFields)) {
            if ($sortField === 'cliente_nome') {
                // Ordenar pelo nome do cliente
                $query->join('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
                    ->orderBy('clientes.nome', $sortDirection)
                    ->select('pedidos.*'); // evita conflito de colunas
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            // Ordem padrão
            $query->orderBy('id', 'desc');
        }

        $pedidos = $query->get();

        return view('producao.index', compact(
            'pedidos', 'id', 'clienteNome', 'endereco', 'telefone',
            'andamento', 'mes', 'tapeceiro', 'etapa', 'dataInicio', 'dataTermino', 'data',
            'sortField', 'sortDirection', 'customOrder'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'andamento' => 'required|string',
            'tapeceiro' => 'nullable|string',
            'prazo' => 'nullable|string',
            'previsao_entrega' => 'nullable|date',
            'pronto_dia' => 'nullable|date',
            'data_inicio' => 'nullable|date',
            'data_retirada' => 'nullable|date',
            'observacao' => 'nullable|string',
        ]);

        $pedido = Pedido::findOrFail($id);

        $pedido->andamento = strtolower($request->input('andamento'));
        $pedido->tapeceiro = $request->input('tapeceiro');
        $pedido->prazo = $request->input('prazo');
        $pedido->data_inicio = $request->input('data_inicio');
        $pedido->data_termino = $request->input('pronto_dia');
        $pedido->previsto_para = $request->input('previsao_entrega');
        $pedido->data_retirada = $request->input('data_retirada');
        $pedido->obs = $request->input('observacao');
        $pedido->obs_retirada = $request->input('obs_retirada'); // <- adiciona aqui
        $pedido->save();

        return redirect()->route('producao.index')->with('success', 'Pedido atualizado com sucesso!');
    }
}
