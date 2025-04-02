<!-- resources/views/clientes/index.blade.php -->
<x-app-layout>

    <div class="container">
        <h1>Clientes</h1>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">Cadastrar Cliente</a>

        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->nome }}</td>
                        <td>{{ $cliente->telefone }}</td>
                        <td>{{ $cliente->email }}</td>
                        <td>
<a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-warning">Editar</a>
<form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" style="display:inline;">
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
