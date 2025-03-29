<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Caso tenha um arquivo CSS -->
</head>
<body>
    <div class="container">
        <header>
            <h1>Bem-vindo à Tapeçaria XYZ!</h1>
            <p>Oferecemos serviços de alta qualidade para reformar e customizar seus móveis. Descubra como podemos ajudar a transformar seu espaço!</p>
        </header>


           <div class="container">
        <div class="jumbotron">
            <h1>Bem-vindo ao Sistema!</h1>
            <p>Este é um sistema de exemplo. Aqui você pode realizar o login ou criar uma nova conta.</p>

            <!-- Botões para Login e Cadastro -->
            <div class="mt-4">
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                <a href="{{ route('register') }}" class="btn btn-secondary">Cadastrar-se</a>
            </div>
        </div>
    </div>
    
       

    <script src="{{ asset('js/app.js') }}"></script> <!-- Caso tenha um arquivo JS -->
</body>
</html>
