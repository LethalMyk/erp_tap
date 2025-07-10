<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\PedidoImagem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Agendamento;
use App\Models\Terceirizadas;

class PedidoController extends Controller
{
    public function index(Request $request)
    {
        $id = $request->input('id');
        $clienteNome = $request->input('cliente_nome');
        $endereco = $request->input('endereco');
        $telefone = $request->input('telefone');
        $data = $request->input('data');
        $andamento = $request->input('andamento');
        $tapeceiro = $request->input('tapeceiro');
        $mes = $request->input('mes');

        // Parâmetros para ordenação
        $sortField = $request->input('sort_field');
        $sortDirection = $request->input('sort_direction', 'desc'); // padrão desc

        // Parâmetro para ordenação customizada
        $customOrder = $request->input('custom_order');

        // Campos permitidos para ordenar
        $allowedSortFields = ['id', 'data', 'andamento', 'tapeceiro', 'prazo'];

        $query = Pedido::with(['cliente', 'imagens'])
            ->when($id, fn($query) => $query->where('id', $id))
            ->when($clienteNome, fn($query) => $query->whereHas('cliente', fn($q) => $q->where('nome', 'like', '%' . $clienteNome . '%')))
            ->when($endereco, fn($query) => $query->whereHas('cliente', fn($q) => $q->where('endereco', 'like', '%' . $endereco . '%')))
            ->when($telefone, fn($query) => $query->whereHas('cliente', fn($q) => $q->where('telefone', 'like', '%' . $telefone . '%')))
            ->when($data, fn($query) => $query->whereDate('data', $data))
            ->when($andamento, function ($query) use ($andamento) {
                if (is_array($andamento)) {
                    return $query->whereIn('andamento', $andamento);
                }
                return $query->where('andamento', 'like', '%' . $andamento . '%');
            })
            ->when($tapeceiro, fn($query) => $query->where('tapeceiro', 'like', '%' . $tapeceiro . '%'))
            ->when($mes, fn($query) => $query->whereMonth('prazo', '=', $mes));

        // Ordenação customizada no tapeceiro via CASE + LIKE (mais flexível)
        if ($customOrder) {
            $names = array_map('trim', explode(',', $customOrder));
            $names = array_map(function ($name) {
                return addslashes($name);
            }, $names);

            $orderCases = [];
            foreach ($names as $index => $name) {
                // Ordena quem contém o nome em tapeceiro, na ordem do array
                $orderCases[] = "WHEN tapeceiro LIKE '%$name%' THEN $index";
            }
            $orderRaw = "CASE " . implode(' ', $orderCases) . " ELSE " . count($names) . " END";

            $query->orderByRaw($orderRaw);
        }
        // Ordenação normal
        elseif (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        $pedidos = $query->get();

        return view('pedidos.index', compact(
            'pedidos', 'id', 'clienteNome', 'endereco', 'telefone', 'data', 'andamento',
            'tapeceiro', 'mes', 'sortField', 'sortDirection', 'customOrder'
        ));
    }

    // Método para exibir o formulário de criação de pedido
    public function create()
    {
        $clientes = Cliente::all();
        return view('pedidos.create', compact('clientes'));
    }

    // Método para armazenar um novo pedido
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'qntItens' => 'required|integer',
            'data' => 'required|date',
            'valor' => 'required|numeric',
            'obs' => 'nullable|string',
            'prazo' => 'required|date',
            'imagens.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // Criar pedido com status "RESTA" e valorResta igual ao valor inicial
        $pedido = Pedido::create([
            'cliente_id' => $request->cliente_id,
            'qntItens' => $request->qntItens,
            'data' => $request->data,
            'valor' => $request->valor,
            'valorResta' => $request->valor,
            'status' => 'RESTA',
            'obs' => $request->obs,
            'prazo' => $request->prazo,
            'andamento' => 'Retirar',
        ]);

        // Armazenamento de imagens (se houver)
        if ($request->hasFile('imagens')) {
            foreach ($request->file('imagens') as $imagem) {
                $path = $imagem->store('pedidos', 'public');
                PedidoImagem::create([
                    'pedido_id' => $pedido->id,
                    'imagem' => $path
                ]);
            }
        }

        // Buscar dados do cliente para agendamento
        $cliente = Cliente::find($request->cliente_id);

        // Criar agendamento automático após criação do pedido
        Agendamento::create([
            'tipo' => 'retirada',
            'data' => $request->input('data_retirada'),
            'horario' => '08:00',
            'nome_cliente' => $cliente->nome ?? '',
            'endereco' => $cliente->endereco ?? '',
            'telefone' => $cliente->telefone ?? '',
            'status' => 'pendente',
            'items' => 'Pedido #' . $pedido->id,
            'obs' => 'Agendamento automático gerado pelo pedido.',
        ]);

        return redirect()->route('pedidos.index')->with('success', 'Pedido criado com sucesso!');
    }

    // Método para exibir o formulário de edição de pedido
    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::all();
        return view('pedidos.edit', compact('pedido', 'clientes'));
    }

    // Método para atualizar um pedido
    public function update(Request $request, $id)
    {
        $pedido = Pedido::findOrFail($id);

        // Atualiza dados rápidos do pedido (ex: modal de edição)
        if ($request->hasAny(['andamento', 'status'])) {
            $pedido->update($request->only(['andamento', 'status']));
            return redirect()->back()->with('success', 'Pedido atualizado com sucesso!');
        }

        // Atualiza os dados do cliente (caso esteja vindo de outra view)
        if ($request->has('cliente')) {
            $clienteData = $request->input('cliente');
            $pedido->cliente->update($clienteData);
        }

        // Atualiza os dados do pedido (completo)
        $pedido->update($request->only([
            'data',
            'prazo',
            'data_retirada'
        ]));

        return redirect()->back()->with('success', 'Cliente e pedido atualizados com sucesso!');
    }

    // Método para exibir os detalhes de um pedido
    public function show($id)
    {
        $pedido = Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
        return view('pedidos.show', compact('pedido'));
    }

    public function destroyImagem(Pedido $pedido, PedidoImagem $imagem)
    {
        // Verifica se a imagem pertence ao pedido correto
        if ($imagem->pedido_id !== $pedido->id) {
            return redirect()->route('pedidos.show', $pedido->id)->with('error', 'Imagem não pertence a este pedido.');
        }

        // Exclui a imagem do armazenamento
        Storage::disk('public')->delete($imagem->imagem);

        // Remove do banco de dados
        $imagem->delete();

        return redirect()->route('pedidos.show', $pedido->id)->with('success', 'Imagem excluída com sucesso.');
    }

    // Impressão da via padrão TAP
    public function imprimirviatap($id)
    {
        $pedido = Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
        return view('pedidos.vias.imprimirviatap', compact('pedido'));
    }

    // Impressão da via padrão Retirada
    public function imprimirviaretirada($id)
    {
        $pedido = Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
        return view('pedidos.vias.imprimirviaretirada', compact('pedido'));
    }

    // Impressão da via completa
    public function imprimirviacompleta($id)
    {
        $pedido = Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
        return view('pedidos.vias.imprimirviacompleta', compact('pedido'));
    }

    // Impressão da via simplificada
    public function imprimirviasimplificada($id)
    {
        $pedido = Pedido::with(['cliente', 'items.terceirizadas', 'pagamentos', 'imagens'])->findOrFail($id);
        return view('pedidos.vias.imprimirviasimplificada', compact('pedido'));
    }

}
