<x-app-layout>

<div class="container">
        <h1>Detalhes do Cliente</h1>

        <ul>
            <li><strong>Nome:</strong> {{ $cliente->nome }}</li>
            <li><strong>Telefone:</strong> {{ $cliente->telefone }}</li>
            <li><strong>Email:</strong> {{ $cliente->email }}</li>
            <li><strong>CPF:</strong> {{ $cliente->cpf }}</li>
        </ul>

        <a href="{{ route('clientes.index') }}" class="btn btn-primary">Voltar</a>
    </div>
</x-app-layout>
