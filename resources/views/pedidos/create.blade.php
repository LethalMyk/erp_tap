<!DOCTYPE html>
<html lang="pt-br">

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cadastro de Cliente e Pedido') }}
        </h2>
    </x-slot>

<head>
    <meta charset="UTF-8">
    <title>Criar Pedido</title>
</head>
<body>

    <form action="{{ route('pedidos.store') }}" method="POST">
        @csrf

        <!-- Dados do Cliente -->
        <label for="nome_cliente">Nome do Cliente:</label>
        <input type="text" name="nome_cliente" id="nome_cliente" required>
        <br><br>

        <label for="telefone_cliente">Telefone:</label>
        <input type="text" name="telefone_cliente" id="telefone_cliente" required>
        <br><br>

        <label for="endereco_cliente">Endereço:</label>
        <input type="text" name="endereco_cliente" id="endereco_cliente" required>
        <br><br>

        <label for="email_cliente">Email:</label>
        <input type="email" name="email_cliente" id="email_cliente" required>
        <br><br>

        <label for="cpf_cliente">CPF:</label>
        <input type="text" name="cpf_cliente" id="cpf_cliente" required>
        <br><br>

        <!-- Dados do Pedido -->
        <h3>Dados do Pedido</h3>
        <label for="data">Data:</label>
        <input type="date" name="data" id="data" required>
        <br><br>

        <label for="orcamento">Orçamento:</label>
        <input type="number" name="orcamento" id="orcamento" required>
        <br><br>

        <label for="status">Status:</label>
        <input type="text" name="status" id="status" required>
        <br><br>

        <label for="prazo">Prazo:</label>
        <input type="date" name="prazo" id="prazo" required>
        <br><br>

        <label for="data_retirada">Data de Retirada:</label>
        <input type="date" name="data_retirada" id="data_retirada" required>
        <br><br>

        <label for="obs">Observações:</label>
        <textarea name="obs" id="obs"></textarea>
        <br><br>

        <!-- Itens -->
        <div id="items">
            <h3>Itens</h3>
            <div class="item">
                <label for="nome_item[]">Nome do Item:</label>
                <input type="text" name="items[0][nome_item]" required>
                <br><br>

                <label for="material[]">Material:</label>
                <input type="text" name="items[0][material]" required>
                <br><br>

                <label for="metragem[]">Metragem:</label>
                <input type="number" name="items[0][metragem]" required>
                <br><br>

                <label for="especificacao[]">Especificação:</label>
                <input type="text" name="items[0][especificacao]">
                <br><br>
            </div>
        </div>

        <!-- Botão para adicionar mais itens -->
        <button type="button" id="addItemButton">Adicionar Item</button>
        <br><br>

        <!-- Pagamento -->
        <h3>Pagamento</h3>
        <label for="valor_pagamento">Valor:</label>
        <input type="number" name="valor_pagamento" id="valor_pagamento" required>
        <br><br>

        <label for="forma_pagamento">Forma de Pagamento:</label>
        <input type="text" name="forma_pagamento" id="forma_pagamento" required>
        <br><br>

        <button type="submit">Registrar Pedido</button>
    </form>

    <script>
        // Script para adicionar mais itens
        let itemCount = 1;
        document.getElementById('addItemButton').addEventListener('click', function() {
            if (itemCount < 10) {
                itemCount++;
                const newItem = document.createElement('div');
                newItem.classList.add('item');
                newItem.innerHTML = ` 
                    <label for="nome_item[]">Nome do Item:</label>
                    <input type="text" name="items[${itemCount}][nome_item]" required>
                    <br><br>
                    <label for="material[]">Material:</label>
                    <input type="text" name="items[${itemCount}][material]" required>
                    <br><br>
                    <label for="metragem[]">Metragem:</label>
                    <input type="number" name="items[${itemCount}][metragem]" required>
                    <br><br>
                    <label for="especificacao[]">Especificação:</label>
                    <input type="text" name="items[${itemCount}][especificacao]">
                    <br><br>
                `;
                document.getElementById('items').appendChild(newItem);
            }
        });
    </script>
</body>
</html>
</x-app-layout>

