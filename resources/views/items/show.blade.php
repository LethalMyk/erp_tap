@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detalhes do Item</h1>

    <ul>
        <li><strong>Nome:</strong> {{ $item->nome_item }}</li>
        <li><strong>Quantidade:</strong> {{ $item->quant_item }}</li>
        <li><strong>Tecido:</strong> {{ $item->tecido_item }}</li>
        <li><strong>Metragem:</strong> {{ $item->metragem_item }}</li>
        <li><strong>Descrição:</strong> {{ $item->desc_item }}</li>
        <li><strong>Observações:</strong> {{ $item->obs_item }}</li>
    </ul>

    <a href="{{ route('pedidos.itens
