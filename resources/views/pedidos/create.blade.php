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
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
#removePagamentoButton {
    display: inline-block;
    padding: 5px 10px;
    font-size: 14px;
    background-color: #dc3545; /* Vermelho para remoção */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 10px; /* Pequeno espaço entre os botões */
}

#removePagamentoButton:hover {
    background-color: #c82333;
}

#removePagamentoButton {
    width: auto;
}

        #addPagamentoButton {
    display: inline-block;
    padding: 5px 10px; /* Reduz o padding para diminuir o tamanho */
    font-size: 14px; /* Reduz a fonte */
    background-color:rgb(217, 238, 161); /* Verde para diferenciar dos outros */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}

#addPagamentoButton:hover {
    background-color:rgb(209, 223, 18);
}
#addPagamentoButton {
    width: auto;
}
        /* Estilo para organizar os campos lado a lado */
        .cliente-dados, .dados-pedido, .itens {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px; /* Espaçamento entre as seções */
            padding-bottom: 20px;
            border-bottom: 2px solid #ccc; /* Linha de separação */
        }

        .campo {
            flex: 1;
            min-width: 250px; /* Define a largura mínima para os campos */
        }

        .campo label {
            display: block;
            margin-bottom: 5px;
        }

        .campo input, .campo textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Estilo para os campos de itens e pagamento */
        #items .item {
            margin-bottom: 20px;
        }

        /* Botão de adicionar item */
        #addItemButton, #removeItemButton {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #addItemButton:hover, #removeItemButton:hover {
            background-color: #0056b3;
        }

        #removeItemButton {
            background-color: #dc3545; /* Cor vermelha para remover */
        }
    </style>
</head>

<body>
    <form action="{{ route('pedidos.store') }}" method="POST">
        @csrf

        <!-- Cliente Dados -->
        <div class="cliente-dados">
            <div class="campo">
                <label for="nome_cliente">Nome do Cliente:</label>
                <input type="text" name="nome_cliente" id="nome_cliente" required>
            </div>
            
            <div class="campo">
                <label for="telefone_cliente">Telefone:</label>
                <input type="text" name="telefone_cliente" id="telefone_cliente" required>
            </div>

            <div class="campo">
                <label for="endereco_cliente">Endereço:</label>
                <input type="text" name="endereco_cliente" id="endereco_cliente" required>
            </div>
        </div>

        <div class="cliente-dados">
            <div class="campo">
                <label for="email_cliente">Email:</label>
                <input type="email" name="email_cliente" id="email_cliente" required>
            </div>

            <div class="campo">
                <label for="cpf_cliente">CPF:</label>
                <input type="text" name="cpf_cliente" id="cpf_cliente" required>
            </div>
        </div>

        <!-- Dados do Pedido -->
        <div class="dados-pedido">
            <h3>Dados do Pedido</h3>
            <div class="campo">
                <label for="data">Data:</label>
                <input type="date" name="data" id="data" required>
            </div>
            <div class="campo">
                <label for="orcamento">Orçamento:</label>
                <input type="number" name="orcamento" id="orcamento" required>
            </div>
            <div class="campo">
                <label for="status">Status:</label>
                <input type="text" name="status" id="status" required>
            </div>
            <div class="campo">
                <label for="prazo">Prazo:</label>
                <input type="date" name="prazo" id="prazo" required>
            </div>
            <div class="campo">
                <label for="data_retirada">Data de Retirada:</label>
                <input type="date" name="data_retirada" id="data_retirada" required>
            </div>
            <div class="campo">
                <label for="obs">Observações:</label>
                <textarea name="obs" id="obs"></textarea>
            </div>
        </div>

        <!-- Itens -->
        <div id="items" class="itens">
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

                <!-- Especificação -->
                <div class="campo">
                    <label for="especificacao[]">Especificação:</label>
                    <textarea name="items[0][especificacao]" id="especificacao" placeholder="Adicione a especificação do item..."></textarea>
                </div>
                <br><br>

            </div>
        </div>

        <!-- Botões para adicionar e remover itens -->
        <button type="button" id="addItemButton">Adicionar Item</button>
        <button type="button" id="removeItemButton">Remover Último Item</button>
        <br><br>

        <!-- Descrição -->
        <div class="campo">
            <label for="descricao">Observações gerais do pedido acima:</label>
            <textarea name="descricao" id="descricao" rows="4" cols="50" placeholder="Adicione mais detalhes aqui..."></textarea>
        </div>
        <br><br>

        <!-- Valor do Orçamento -->
        <h3>Valor do Orçamento</h3>
        <div class="campo">
            <label for="valor_total">Valor Total:</label>
            <input type="number" name="valor_total" id="valor_total" required>
        </div>

        <div class="campo">
            <label for="adicionais">Adicionais:</label>
            <input type="number" name="adicionais" id="adicionais" value="0">
        </div>
        <br><br>

<!-- Seção de Pagamento -->
<h3>Pagamento</h3>
<div id="pagamentos">
    <div class="campo pagamento">
        <strong>Pagamento 1</strong>
        <label for="valor_pagamento">Valor:</label>
        <input type="number" name="pagamentos[0][valor]" required>
    </div>
    <div class="campo pagamento">
        <label for="forma_pagamento">Forma de Pagamento:</label>
        <select name="pagamentos[0][forma]" required>
            <option value="Pix">Pix</option>
            <option value="Débito">Débito</option>
            <option value="Crédito À Vista">Crédito À Vista</option>
            <option value="Crédito Parcelado">Crédito Parcelado</option>
            <option value="Boleto">Boleto</option>
            <option value="Promissória">Promissória</option>
        </select>
    </div>
    <div class="campo pagamento">
        <label for="descricao_pagamento">Descrição:</label>
        <input type="text" name="pagamentos[0][descricao]" required>
    </div>
    <hr> <!-- Linha separadora -->
</div>

<!-- Botão para adicionar mais pagamentos -->
<button type="button" id="addPagamentoButton">Adicionar Novo Pagamento</button>
<button type="button" id="removePagamentoButton">Remover Último Pagamento</button>


<script>
    let pagamentoCount = 1;

    // Adicionar novo pagamento
    document.getElementById('addPagamentoButton').addEventListener('click', function () {
        const pagamentosContainer = document.getElementById('pagamentos');

        // Criar novo pagamento numerado
        const newPagamento = document.createElement('div');
        newPagamento.classList.add('campo', 'pagamento');

        newPagamento.innerHTML = `
            <strong>Pagamento ${pagamentoCount + 1}</strong>
            <label for="valor_pagamento">Valor:</label>
            <input type="number" name="pagamentos[${pagamentoCount}][valor]" required>

            <label for="forma_pagamento">Forma de Pagamento:</label>
            <select name="pagamentos[${pagamentoCount}][forma]" required>
                <option value="Pix">Pix</option>
                <option value="Débito">Débito</option>
                <option value="Crédito À Vista">Crédito À Vista</option>
                <option value="Crédito Parcelado">Crédito Parcelado</option>
                <option value="Boleto">Boleto</option>
                <option value="Promissória">Promissória</option>
            </select>

            <label for="descricao_pagamento">Descrição:</label>
            <input type="text" name="pagamentos[${pagamentoCount}][descricao]" required>

            <hr> <!-- Linha separadora -->
        `;

        pagamentosContainer.appendChild(newPagamento);
        pagamentoCount++;
    });
</script>
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

                    <!-- Especificação -->
                    <label for="especificacao[]">Especificação:</label>
                    <textarea name="items[${itemCount}][especificacao]" placeholder="Adicione a especificação do item..."></textarea>
                    <br><br>
                `;

                document.getElementById('items').appendChild(newItem);
            }
        });

        // Script para remover o último item
        document.getElementById('removeItemButton').addEventListener('click', function() {
            if (itemCount > 1) {
                const lastItem = document.getElementById('items').lastElementChild;
                document.getElementById('items').removeChild(lastItem);
                itemCount--;
            }
        });

        document.getElementById('removePagamentoButton').addEventListener('click', function () {
    const pagamentosContainer = document.getElementById('pagamentos');
    
    if (pagamentoCount > 1) { // Garante que pelo menos um pagamento sempre fique
        pagamentosContainer.removeChild(pagamentosContainer.lastElementChild);
        pagamentoCount--;
    } else {
        alert("Não é possível remover o último pagamento!");
    }
});


    </script>
</body>
</html>
</x-app-layout>
