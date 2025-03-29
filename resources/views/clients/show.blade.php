@extends('layouts.app')

@section('content')
    <h1>Detalhes do Cliente</h1>
    <p><strong>Nome:</strong> {{ $client->nome }}</p>
    <p><strong>Telefone:</strong> {{ $client->telefone }}</p>
    <p><strong>Endereço:</strong> {{ $client->endereco }}</p>
    <p><strong>Email:</strong> {{ $client->email }}</p>
    <p><strong>CPF:</strong> {{ $client->cpf }}</p>

    <a href="{{ route('clients.index') }}" class="btn btn-primary">Voltar</a>
@endsection
