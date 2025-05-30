<x-app-layout>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vias - Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tab-content {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Pedidos/Vias</h1>
        <!-- Abas -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="viacompleta-tab" data-bs-toggle="tab" href="#viacompleta" role="tab" aria-controls="viacompleta" aria-selected="true">Via Completa</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="viaretirada-tab" data-bs-toggle="tab" href="#viaretirada" role="tab" aria-controls="viaretirada" aria-selected="false">Via Retirada</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="viatap-tab" data-bs-toggle="tab" href="#viatap" role="tab" aria-controls="viatap" aria-selected="false">Via TAP</a>
            </li>
        </ul>

        <!-- Conteúdo das abas -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="viacompleta" role="tabpanel" aria-labelledby="viacompleta-tab">
                @include('pedidos.vias.viacompleta')
            </div>
            <div class="tab-pane fade" id="viaretirada" role="tabpanel" aria-labelledby="viaretirada-tab">
                @include('pedidos.vias.viaretirada')
            </div>
            <div class="tab-pane fade" id="viatap" role="tabpanel" aria-labelledby="viatap-tab">
                @include('pedidos.vias.viatap')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
    </x-app-layout>
