<x-app-layout>
    <form method="GET" action="{{ route('pagamento.index') }}" style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
    <div>
    <label for="nome" style="font-weight: 600; margin-right: 5px;">Nome:</label>
    <input type="text" name="nome" id="nome" value="{{ request('nome') }}" 
           style="padding: 6px; border-radius: 4px; border: 1px solid #ccc;" 
           placeholder="Nome do cliente">
</div>

<div>
    <label for="endereco" style="font-weight: 600; margin-right: 5px;">Endereço:</label>
    <input type="text" name="endereco" id="endereco" value="{{ request('endereco') }}" 
           style="padding: 6px; border-radius: 4px; border: 1px solid #ccc;" 
           placeholder="Endereço do cliente">
</div>
<div>
    <label for="data_inicio" style="font-weight: 600; margin-right: 5px;">Data Início:</label>
    <input type="date" name="data_inicio" id="data_inicio" value="{{ request('data_inicio') }}"
           style="padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
</div>

<div>
    <label for="data_fim" style="font-weight: 600; margin-right: 5px;">Data Fim:</label>
    <input type="date" name="data_fim" id="data_fim" value="{{ request('data_fim') }}"
           style="padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
</div>

    <div>
        <label for="status" style="font-weight: 600; margin-right: 5px;">Status:</label>
        <select name="status" id="status" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="">Todos</option>
            <option value="EM ABERTO" {{ (request('status') == 'EM ABERTO') ? 'selected' : '' }}>EM ABERTO</option>
            <option value="PAGAMENTO REGISTRADO" {{ (request('status') == 'PAGAMENTO REGISTRADO') ? 'selected' : '' }}>PAGAMENTO REGISTRADO</option>
        </select>
    </div>

    <div>
        <label for="forma" style="font-weight: 600; margin-right: 5px;">Forma:</label>
        <select name="forma" id="forma" style="padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="">Todas</option>
            <option value="PIX" {{ (request('forma') == 'PIX') ? 'selected' : '' }}>PIX</option>
            <option value="DEBITO" {{ (request('forma') == 'DEBITO') ? 'selected' : '' }}>DEBITO</option>
            <option value="DINHEIRO" {{ (request('forma') == 'DINHEIRO') ? 'selected' : '' }}>DINHEIRO</option>
            <option value="CREDITO À VISTA" {{ (request('forma') == 'CREDITO À VISTA') ? 'selected' : '' }}>CREDITO À VISTA</option>
            <option value="CREDITO PARCELADO" {{ (request('forma') == 'CREDITO PARCELADO') ? 'selected' : '' }}>CREDITO PARCELADO</option>
            <option value="BOLETO" {{ (request('forma') == 'BOLETO') ? 'selected' : '' }}>BOLETO</option>
            <option value="CHEQUE" {{ (request('forma') == 'CHEQUE') ? 'selected' : '' }}>CHEQUE</option>
            <option value="OUTROS" {{ (request('forma') == 'OUTROS') ? 'selected' : '' }}>OUTROS</option>
        </select>
    </div>

    <div>
        <button type="submit" style="
            background-color: #007bff; 
            color: white; 
            border: none; 
            padding: 8px 16px; 
            border-radius: 4px; 
            font-weight: 600; 
            cursor: pointer;
        ">
            Filtrar
        </button>
    </div>

    <div>
        <a href="{{ route('pagamento.index') }}" style="
            padding: 8px 16px;
            border-radius: 4px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            display: inline-block;
        ">
            Limpar
        </a>
    </div>
</form>

    <div class="container" style="max-width: 100%; padding: 0 30px; margin: 30px auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 20px; color: #222;">Lista de Pagamentos</h1>

        <div style="text-align: right; margin-bottom: 20px;">
            <a href="{{ route('pagamento.create') }}" 
               style="
                   background-color: #28a745; 
                   color: white; 
                   padding: 10px 20px; 
                   text-decoration: none; 
                   border-radius: 6px; 
                   font-weight: 600;
                   box-shadow: 0 4px 8px rgb(40 167 69 / 0.3);
                   transition: background-color 0.3s ease;
               "
               onmouseover="this.style.backgroundColor='#218838';"
               onmouseout="this.style.backgroundColor='#28a745';"
            >
                + Novo Pagamento
            </a>
        </div>

        @if(session('success'))
            <div style="
                background-color: #d4edda; 
                color: #155724; 
                padding: 12px 20px; 
                border-radius: 6px; 
                margin-bottom: 20px;
                border: 1px solid #c3e6cb;
                box-shadow: 0 2px 4px rgb(40 167 69 / 0.15);
            ">
                {{ session('success') }}
            </div>
        @endif

        <div style="overflow-x:auto; box-shadow: 0 2px 8px rgb(0 0 0 / 0.1); border-radius: 8px;">
            <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
                <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <tr>
                        <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #495057;">Pedido ID</th>
                        <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #495057;">Cliente</th>
                        <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #495057;">Endereço</th>
                        <th style="padding: 12px 15px; text-align: right; font-weight: 600; color: #495057;">Valor Total  do Pedido</th>
                        <th style="padding: 12px 15px; text-align: right; font-weight: 600; color: #495057;">Valor deste Pagamento</th>
                        <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #495057;">Data deste Pagamento</th>
                        <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #495057;">Status deste Pagamento</th>
                        <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #495057;">Forma</th>
                        <th style="padding: 12px 15px; text-align: left; font-weight: 600; color: #495057;">Observação</th>
                        <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #495057;">Status do Pedido TOTAL</th>
                        <th style="padding: 12px 15px; text-align: right; font-weight: 600; color: #495057;">Valor Restante do Pedido</th>
                        <th style="padding: 12px 15px; text-align: center; font-weight: 600; color: #495057;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagamentos as $pagamento)
                        <tr style="border-bottom: 1px solid #e9ecef; transition: background-color 0.2s;">
                            <td style="padding: 12px 15px;">{{ $pagamento->pedido->id ?? 'N/A' }}</td>
                            <td style="padding: 12px 15px;">{{ $pagamento->pedido->cliente->nome ?? 'N/A' }}</td>
                            <td style="padding: 12px 15px;">{{ $pagamento->pedido->cliente->endereco ?? 'N/A' }}</td>
                            <td style="padding: 12px 15px; text-align: right;">R$ {{ number_format($pagamento->pedido->valor ?? 0, 2, ',', '.') }}</td>
                            <td style="padding: 12px 15px; text-align: right;">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</td>
                            <td style="padding: 12px 15px; text-align: center;">
    {{ $pagamento->data ? \Carbon\Carbon::parse($pagamento->data)->format('d/m/Y') : '—' }}
</td>
                            <td style="padding: 12px 15px; text-align: center;">
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 20px; 
                                    background-color: 
                                    {{ $pagamento->status === 'PAGAMENTO REGISTRADO' ? '#28a745' : '#ffc107' }};
                                    color: white; font-weight: 600; font-size: 0.85rem;">
                                    {{ $pagamento->status }}
                                </span>
                            </td>
                            <td style="padding: 12px 15px; text-align: center;">{{ $pagamento->forma }}</td>
                            <td style="padding: 12px 15px; max-width: 250px; white-space: normal; word-break: break-word;">{{ $pagamento->obs }}</td>
                            <td style="padding: 12px 15px; text-align: center;">{{ $pagamento->pedido->status ?? 'N/A' }}</td>
                            <td style="padding: 12px 15px; text-align: right;">
    R$ {{ number_format($pagamento->valor_resta_pedido ?? 0, 2, ',', '.') }}
</td>

                            <td style="padding: 12px 15px; text-align: center; vertical-align: middle;">

                            

                                @if($pagamento->status === 'EM ABERTO')
                                    <button type="button" onclick="toggleRegistrar({{ $pagamento->id }})"
                                        style="
                                            background-color: #ffc107; 
                                            border: none; 
                                            color: #212529; 
                                            padding: 6px 12px; 
                                            border-radius: 4px; 
                                            font-weight: 600; 
                                            cursor: pointer;
                                            margin-right: 10px;
                                        ">
                                        Registrar
                                    </button>

                                    <div id="registrar-form-{{ $pagamento->id }}" style="display: none; margin-top: 8px;">
                                        <form action="{{ route('pagamento.registrar', $pagamento->id) }}" method="POST" style="max-width: 300px;">
                                            @csrf
                                            <textarea name="obs" rows="2" placeholder="Observação (opcional)" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ced4da;"></textarea>

                                            <label for="data_registro_{{ $pagamento->id }}" style="display: block; margin: 8px 0 4px;">Data do Registro:</label>
                                            <input type="date" name="data_registro" id="data_registro_{{ $pagamento->id }}" value="{{ date('Y-m-d') }}" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ced4da; margin-bottom: 8px;" />

                                            <button type="submit" style="
                                                background-color: #28a745; 
                                                color: white; 
                                                border: none; 
                                                padding: 8px 16px; 
                                                border-radius: 4px; 
                                                font-weight: 600; 
                                                cursor: pointer;
                                            ">Confirmar Registro</button>
                                        </form>
                                    </div>
                                @endif

                                <form action="{{ route('pagamento.destroy', $pagamento->id) }}" method="POST" style="display:inline-block; margin-top: 5px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        onclick="return confirm('Tem certeza que deseja excluir?');"
                                        style="
                                            background-color: #dc3545;
                                            border: none;
                                            color: white;
                                            padding: 6px 12px;
                                            border-radius: 4px;
                                            font-weight: 600;
                                            cursor: pointer;
                                        ">
                                        Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding: 20px; color: #6c757d;">
                                Nenhum pagamento encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleRegistrar(id) {
            const form = document.getElementById('registrar-form-' + id);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</x-app-layout>
