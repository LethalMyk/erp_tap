<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nova Despesa</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto py-8 px-4">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('despesas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="descricao" class="block font-medium text-gray-700">Descrição</label>
                <input type="text" name="descricao" id="descricao" value="{{ old('descricao') }}" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            </div>

            <div>
                <label for="valor" class="block font-medium text-gray-700">Valor</label>
                <input type="number" step="0.01" name="valor" id="valor" value="{{ old('valor') }}" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            </div>

            <div>
                <label for="data_vencimento" class="block font-medium text-gray-700">Data Vencimento</label>
                <input type="date" name="data_vencimento" id="data_vencimento" value="{{ old('data_vencimento') }}" required
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            </div>

            <div>
                <label for="data_pagamento" class="block font-medium text-gray-700">Data Pagamento</label>
                <input type="date" name="data_pagamento" id="data_pagamento" value="{{ old('data_pagamento') }}"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            </div>

            <div>
                <label for="status" class="block font-medium text-gray-700">Status</label>
                <select name="status" id="status" required class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                    @foreach(['PENDENTE', 'PAGO', 'ATRASADO'] as $status)
                        <option value="{{ $status }}" @selected(old('status') == $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="categoria" class="block font-medium text-gray-700">Categoria</label>
                <select name="categoria" id="categoria" required class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                    @foreach(['FORNECEDOR', 'AGUA', 'LUZ', 'MATERIAL', 'PARTICULAR', 'OUTROS'] as $cat)
                        <option value="{{ $cat }}" @selected(old('categoria') == $cat)>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="forma_pagamento" class="block font-medium text-gray-700">Forma Pagamento</label>
                <select name="forma_pagamento" id="forma_pagamento" required class="mt-1 block w-full rounded border-gray-300 shadow-sm">
                    @foreach(['PIX', 'DINHEIRO', 'BOLETO', 'CARTAO', 'TRANSFERENCIA', 'OUTROS'] as $fp)
                        <option value="{{ $fp }}" @selected(old('forma_pagamento') == $fp)>{{ $fp }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="chave_pagamento" class="block font-medium text-gray-700">Chave Pagamento</label>
                <input type="text" name="chave_pagamento" id="chave_pagamento" value="{{ old('chave_pagamento') }}"
                    class="mt-1 block w-full rounded border-gray-300 shadow-sm" />
            </div>

            <div>
                <label for="comprovante" class="block font-medium text-gray-700">Comprovante (jpg, png, pdf)</label>
                <input type="file" name="comprovante" id="comprovante" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full" />
            </div>

            <div>
                <label for="observacao" class="block font-medium text-gray-700">Observação</label>
                <textarea name="observacao" id="observacao" rows="3" class="mt-1 block w-full rounded border-gray-300 shadow-sm">{{ old('observacao') }}</textarea>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Salvar
                </button>
                <a href="{{ route('despesas.index') }}" class="ml-4 text-gray-600 hover:underline">Cancelar</a>
            </div>
        </form>
    </div>
</x-app-layout>
