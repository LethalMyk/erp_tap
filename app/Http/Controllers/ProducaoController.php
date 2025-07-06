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

        $pedidos = Pedido::with(['cliente', 'servicos.profissional'])
            ->when($data, function($query) use ($data) {
                return $query->whereDate('data', $data);
            })
            ->when($id, function($query) use ($id) {
                return $query->where('id', $id);
            })
            ->when($tapeceiro, function($query) use ($tapeceiro) {
                return $query->where('tapeceiro', 'like', '%' . $tapeceiro . '%');
            })
            ->when($etapa, function($query) use ($etapa) {
                return $query->whereHas('servicos', function($query) use ($etapa) {
                    return $query->where('tipo_servico', $etapa);
                });
            })
            ->when($dataInicio, function($query) use ($dataInicio) {
                return $query->whereHas('servicos', function($query) use ($dataInicio) {
                    return $query->whereDate('data_inicio', '>=', $dataInicio);
                });
            })
            ->when($dataTermino, function($query) use ($dataTermino) {
                return $query->whereHas('servicos', function($query) use ($dataTermino) {
                    return $query->whereDate('data_termino', '<=', $dataTermino);
                });
            })
            ->when($clienteNome, function($query) use ($clienteNome) {
                return $query->whereHas('cliente', function($query) use ($clienteNome) {
                    $query->where('nome', 'like', '%' . $clienteNome . '%');
                });
            })
            ->when($endereco, function($query) use ($endereco) {
                return $query->whereHas('cliente', function($query) use ($endereco) {
                    $query->where('endereco', 'like', '%' . $endereco . '%');
                });
            })
            ->when($telefone, function($query) use ($telefone) {
                return $query->whereHas('cliente', function($query) use ($telefone) {
                    $query->where('telefone', 'like', '%' . $telefone . '%');
                });
            })
            ->when($andamento, function($query) use ($andamento) {
                return $query->whereIn('andamento', (array) $andamento);
            })
            ->when($mes, function($query) use ($mes) {
                return $query->whereHas('servicos', function($query) use ($mes) {
                    return $query->whereMonth('data_termino', '=', $mes);
                });
            })
            ->get();

        return view('producao.index', compact(
            'pedidos', 'id', 'clienteNome', 'endereco', 'telefone',
            'andamento', 'mes', 'tapeceiro', 'etapa', 'dataInicio', 'dataTermino', 'data'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'andamento' => 'required|string',
            'status' => 'required|string',
            'tapeceiro' => 'nullable|string',
        ]);

        $pedido = Pedido::findOrFail($id);

        $pedido->andamento = $request->input('andamento');
        $pedido->status = $request->input('status');
        if ($request->has('tapeceiro')) {
            $pedido->tapeceiro = $request->input('tapeceiro');
        }

        $pedido->save();

        return redirect()->route('producao.index')->with('success', 'Pedido atualizado com sucesso!');
    }
}
