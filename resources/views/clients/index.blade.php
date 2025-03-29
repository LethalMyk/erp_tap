<!-- resources/views/clients/index.blade.php -->

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Clientes</title>
</head>
<body>
    <h1>Clientes Registrados</h1>

    <ul>
        @foreach ($clients as $client)
            <li>{{ $client->nome }} - {{ $client->email }}</li>
        @endforeach
    </ul>

    <!-- Exemplo de notificação -->
    <p>Cliente registrado com sucesso!</p>
</body>
</html>
