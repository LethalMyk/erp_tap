@extends('layouts.app')

 <x-app-layout>
<div class="container">
    <h1>Itens do Pedido</h1>
<a href="{{ route('pedidos.itens.create', $pedido->id) }}" class="btn btn-primary">Novo Item</a>
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Nome do Item</th>
                <th>Quantidade</th>
                <th>Valor</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($itens as $item)
                <tr>
                    <td>{{ $item->nome_item }}</td>
                    <td>{{ $item->quant_item }}</td>
                    <td>{{ $item->valor }}</td>
                    <td>
                        <a href="{{ route('pedidos.itens.show', [$pedido->id, $item->id]) }}" class="btn btn-info">Ver</a>
                        <a href="{{ route('pedidos.itens.edit', [$pedido->id, $item->id]) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('pedidos.itens.destroy', [$pedido->id, $item->id]) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</x-app-layout>
