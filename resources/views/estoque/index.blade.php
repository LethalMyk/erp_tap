<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">Estoque Disponível</h2>
    </x-slot>

    <div class="max-w-6xl mx-auto py-8 px-4" x-data="estoqueModal()" x-init="init()">

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded shadow">
                {{ session('success') }}
            </div>
        @endif

        {{-- Botões de ação --}}
        <div class="mb-6 flex gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" 
                    @click="toggleAll()">
                Expandir / Recolher Todos
            </button>

            <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
                    @click="abrirModalNovoItem()">
                + Adicionar Item
            </button>
        </div>

        @php
            $categorias = ['Materia Prima', 'Espumas', 'Tecidos', 'Ferragens', 'Costura'];
            $produtosUnicos = $estoques->groupBy('produto_id');
        @endphp

        {{-- Categorias fixas --}}
        @foreach($categorias as $categoria)
            <div class="mb-4 border rounded shadow-sm">
                <button class="w-full text-left px-4 py-2 bg-gray-100 hover:bg-gray-200 font-semibold rounded-t flex justify-between items-center"
                        @click="toggleCategory('cat-{{ Str::slug($categoria) }}')">
                    {{ $categoria }}
                    <span class="text-gray-500">▼</span>
                </button>

                <div id="cat-{{ Str::slug($categoria) }}" class="hidden">
                    <table class="min-w-full border-t rounded-b">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Produto</th>
                                <th class="px-4 py-2 text-left">Subcategoria</th>
                                <th class="px-4 py-2 text-left">Quantidade</th>
                                <th class="px-4 py-2 text-left">Unidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $produtosCategoria = $produtosUnicos->filter(fn($grupo) => $grupo->first()->produto->categoria === $categoria);
                            @endphp

                            @forelse($produtosCategoria as $grupo)
                                @php
                                    $estoqueProduto = $grupo->first();
                                    $quantidadeTotal = $grupo->map(fn($e) => $e->quantidadeDisponivel())->sum();
                                @endphp
                                <tr class="border-t hover:bg-gray-50 cursor-pointer"
                                    @dblclick="abrirModalMovimento({{ $estoqueProduto->id }}, '{{ $estoqueProduto->produto->nome }}', {{ $quantidadeTotal }})">
                                    <td class="px-4 py-2">{{ $estoqueProduto->produto->nome }}</td>
                                    <td class="px-4 py-2">{{ $estoqueProduto->produto->sub_categoria ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $quantidadeTotal }}</td>
                                    <td class="px-4 py-2">{{ $estoqueProduto->produto->unidade_medida }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-2 text-center text-gray-400">Nenhum produto disponível nesta categoria.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        {{-- Outros / Sem Categoria --}}
        <div class="mb-4 border rounded shadow-sm">
            <button class="w-full text-left px-4 py-2 bg-gray-100 hover:bg-gray-200 font-semibold rounded-t flex justify-between items-center"
                    @click="toggleCategory('cat-outros')">
                Outros / Sem Categoria
                <span class="text-gray-500">▼</span>
            </button>

            <div id="cat-outros" class="hidden">
                <table class="min-w-full border-t rounded-b">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left">Produto</th>
                            <th class="px-4 py-2 text-left">Subcategoria</th>
                            <th class="px-4 py-2 text-left">Quantidade</th>
                            <th class="px-4 py-2 text-left">Unidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $produtosOutros = $estoques->filter(fn($estoque) => !in_array($estoque->produto->categoria, $categorias) || empty($estoque->produto->categoria));
                        @endphp

                        @forelse($produtosOutros as $estoque)
                            <tr class="border-t hover:bg-gray-50 cursor-pointer"
                                @dblclick="abrirModalMovimento({{ $estoque->id }}, '{{ $estoque->produto->nome }}', {{ $estoque->quantidadeDisponivel() }})">
                                <td class="px-4 py-2">{{ $estoque->produto->nome }}</td>
                                <td class="px-4 py-2">{{ $estoque->produto->sub_categoria ?? '-' }}</td>
                                <td class="px-4 py-2">{{ $estoque->quantidadeDisponivel() }}</td>
                                <td class="px-4 py-2">{{ $estoque->produto->unidade_medida }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-center text-gray-400">Nenhum produto disponível nesta categoria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Novo Item --}}
        <div x-show="modalNovoItem" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded shadow-lg w-96 p-6" @click.away="modalNovoItem = false">
                <h3 class="text-lg font-semibold mb-4">Adicionar Item ao Estoque</h3>

                <form method="POST" action="{{ route('estoque.store') }}">
                    @csrf

                    <label class="block mb-2">Produto existente:</label>
                    <select name="produto_existente" x-model="produtoExistente" @change="onProdutoChange"
                            class="w-full border px-3 py-2 rounded mb-3">
                        <option value="">-- Selecionar --</option>
                        @foreach($produtos as $produto)
                            <option value="{{ $produto->id }}" 
                                    data-categoria="{{ $produto->categoria }}" 
                                    data-subcategoria="{{ $produto->sub_categoria ?? '' }}"
                                    data-unidade="{{ $produto->unidade_medida }}">
                                {{ $produto->nome }}
                            </option>
                        @endforeach
                    </select>

                    <div x-show="!produtoExistente">
                        <label class="block mb-2">Nome do produto:</label>
                        <input type="text" name="nome" placeholder="Nome do produto" class="w-full border px-3 py-2 rounded mb-3">
                    </div>

                    <label class="block mb-2">Categoria:</label>
                    <input type="text" name="categoria" x-model="categoria" 
                           :readonly="produtoExistente != ''" class="w-full border px-3 py-2 rounded mb-3">

                    <label class="block mb-2">Subcategoria:</label>
                    <input type="text" name="sub_categoria" x-model="subcategoria" 
                           :readonly="produtoExistente != ''" class="w-full border px-3 py-2 rounded mb-3">

                    <label class="block mb-2">Unidade de Medida:</label>
                    <input type="text" name="unidade_medida" x-model="unidade" 
                           :readonly="produtoExistente != ''" class="w-full border px-3 py-2 rounded mb-3">

                    <input type="hidden" name="categoria" :value="categoria">
                    <input type="hidden" name="sub_categoria" :value="subcategoria">
                    <input type="hidden" name="unidade_medida" :value="unidade">

                    <label class="block mb-2">Quantidade:</label>
                    <input type="number" name="quantidade_inicial" min="0" value="0" class="w-full border px-3 py-2 rounded mb-3">

                    <div class="flex justify-between">
                        <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="modalNovoItem = false">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Movimento Estoque --}}
        <div x-show="modalMovimento" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded shadow-lg w-96 p-6" @click.away="modalMovimento = false">
                <h3 class="text-lg font-semibold mb-4" x-text="nomeMovimento"></h3>

                <p class="mb-2 text-gray-600">Quantidade atual: <span x-text="quantidadeDisponivel"></span></p>

                <form :action="`/estoque/${estoqueId}/movimento`" method="POST" @submit.prevent="processarMovimento($event)">
                    @csrf

                    <label class="block mb-2">Nova quantidade:</label>
                    <input type="number" name="nova_quantidade" x-model="quantidadeMovimento" min="0"
                           placeholder="Digite a quantidade" class="w-full border px-3 py-2 rounded mb-3" required>

                    <input type="hidden" name="tipo" x-model="tipoMovimento">

                    <label class="block mb-2">Descrição:</label>
                    <input type="text" name="descricao" placeholder="Motivo" class="w-full border px-3 py-2 rounded mb-3">

                    <div class="flex justify-between">
                        <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="modalMovimento = false">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

<script>
function estoqueModal() {
    return {
        modalNovoItem: false,
        modalMovimento: false,
        estoqueId: null,
        nomeMovimento: '',
        quantidadeMovimento: 1,
        quantidadeDisponivel: 0,
        tipoMovimento: 'ajuste',
        expandedCategories: {},
        produtoExistente: '',
        categoria: '',
        subcategoria: '',
        unidade: '',

        init() {
            const saved = localStorage.getItem('expandedCategories');
            if(saved) {
                this.expandedCategories = JSON.parse(saved);
                Object.keys(this.expandedCategories).forEach(id => {
                    if(this.expandedCategories[id]) {
                        const el = document.getElementById(id);
                        if(el) el.classList.remove('hidden');
                    }
                });
            }
        },

        abrirModalNovoItem() {
            this.modalNovoItem = true;
            this.produtoExistente = '';
            this.categoria = '';
            this.subcategoria = '';
            this.unidade = '';
        },

        abrirModalMovimento(id, nome, quantidadeDisponivel) {
            this.estoqueId = id;
            this.nomeMovimento = nome;
            this.quantidadeDisponivel = quantidadeDisponivel;
            this.quantidadeMovimento = quantidadeDisponivel; // inicia com valor atual
            this.tipoMovimento = 'ajuste';
            this.modalMovimento = true;
        },

        processarMovimento(event) {
            if(this.quantidadeMovimento > this.quantidadeDisponivel){
                this.tipoMovimento = 'entrada';
            } else if(this.quantidadeMovimento < this.quantidadeDisponivel){
                this.tipoMovimento = 'saida';
            } else {
                alert('A quantidade não foi alterada.');
                return;
            }

            event.target.submit();
        },

        onProdutoChange(event) {
            const selected = event.target.selectedOptions[0];
            if(selected.value) {
                this.categoria = selected.dataset.categoria;
                this.subcategoria = selected.dataset.subcategoria ?? '';
                this.unidade = selected.dataset.unidade;
            } else {
                this.categoria = '';
                this.subcategoria = '';
                this.unidade = '';
            }
        },

        toggleCategory(id) {
            const el = document.getElementById(id);
            if(el) {
                el.classList.toggle('hidden');
                this.expandedCategories[id] = !el.classList.contains('hidden');
                localStorage.setItem('expandedCategories', JSON.stringify(this.expandedCategories));
            }
        },

        toggleAll() {
            const sections = document.querySelectorAll("[id^='cat-']");
            let allHidden = true;
            sections.forEach(el => {
                if(!el.classList.contains('hidden')) allHidden = false;
            });

            sections.forEach(el => {
                if(allHidden) {
                    el.classList.remove('hidden');
                    this.expandedCategories[el.id] = true;
                } else {
                    el.classList.add('hidden');
                    this.expandedCategories[el.id] = false;
                }
            });

            localStorage.setItem('expandedCategories', JSON.stringify(this.expandedCategories));
        }
    }
}
</script>
</x-app-layout>
