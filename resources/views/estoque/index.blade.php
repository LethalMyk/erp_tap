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

        {{-- Botão Expandir / Recolher Todos --}}
        <div class="mb-6">
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" 
                    @click="toggleAll()">
                Expandir / Recolher Todos
            </button>
        </div>

        @php
            $categorias = ['Materia Prima', 'Espumas', 'Tecidos', 'Ferragens', 'Costura'];
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
                                <th class="px-4 py-2 text-left">Quantidade</th>
                                <th class="px-4 py-2 text-left">Unidade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $produtosCategoria = $produtos->filter(fn($estoque) => $estoque->produto->categoria === $categoria);
                            @endphp

                            @forelse($produtosCategoria as $estoque)
                                <tr class="border-t hover:bg-gray-50 cursor-pointer"
                                    @dblclick="abrirModal({{ $estoque->id }}, '{{ $estoque->produto->nome }}', {{ $estoque->quantidade_disponivel ?? 0 }})">
                                    <td class="px-4 py-2">{{ $estoque->produto->nome }}</td>
                                    <td class="px-4 py-2">{{ $estoque->quantidade_disponivel ?? 0 }}</td>
                                    <td class="px-4 py-2">{{ $estoque->produto->unidade_medida }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-center text-gray-400">Nenhum produto disponível nesta categoria.</td>
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
                            <th class="px-4 py-2 text-left">Quantidade</th>
                            <th class="px-4 py-2 text-left">Unidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $produtosOutros = $produtos->filter(fn($estoque) => !in_array($estoque->produto->categoria, $categorias) || empty($estoque->produto->categoria));
                        @endphp

                        @forelse($produtosOutros as $estoque)
                            <tr class="border-t hover:bg-gray-50 cursor-pointer"
                                @dblclick="abrirModal({{ $estoque->id }}, '{{ $estoque->produto->nome }}', {{ $estoque->quantidade_disponivel ?? 0 }})">
                                <td class="px-4 py-2">{{ $estoque->produto->nome }}</td>
                                <td class="px-4 py-2">{{ $estoque->quantidade_disponivel ?? 0 }}</td>
                                <td class="px-4 py-2">{{ $estoque->produto->unidade_medida }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-2 text-center text-gray-400">Nenhum produto disponível nesta categoria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal --}}
        <div x-show="modalOpen" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded shadow-lg w-96 p-6" @click.away="modalOpen = false">
                <h3 class="text-lg font-semibold mb-4">Editar Estoque: <span x-text="nome"></span></h3>

                <form method="POST" :action="`/estoque/${estoqueId}/quantidade`">
                    @csrf
                    @method('PUT')

                    <label class="block mb-2">Quantidade Disponível:</label>
                    <div class="flex items-center space-x-2 mb-4">
                        <button type="button" @click="novaQuantidade = Math.max(0, novaQuantidade - 1)" class="px-3 py-1 bg-gray-200 rounded">-</button>
                        <input type="number" name="quantidade_disponivel" x-model.number="novaQuantidade" min="0" class="w-full border px-3 py-2 rounded text-center">
                        <button type="button" @click="novaQuantidade++" class="px-3 py-1 bg-gray-200 rounded">+</button>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="modalOpen = false">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function estoqueModal() {
            return {
                modalOpen: false,
                estoqueId: null,
                nome: '',
                novaQuantidade: 0,
                expandedCategories: {},

                init() {
                    // Restaurar estado das categorias
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

                abrirModal(id, nome, quantidade) {
                    this.estoqueId = id;
                    this.nome = nome;
                    this.novaQuantidade = Number(quantidade) || 0;
                    this.modalOpen = true;
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
