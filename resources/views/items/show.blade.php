<   x-app-layout>

// Views - resources/views/items/show.blade.php
    <h1>Detalhes do Item</h1>
    <p><strong>Nome:</strong> {{ $item->nomeItem }}</p>
    <p><strong>Material:</strong> {{ $item->material }}</p>
    <p><strong>Metragem:</strong> {{ $item->metragem }}</p>
    <p><strong>Especificações:</strong> {{ $item->especifi }}</p>
    <a href="{{ route('items.index') }}">Voltar</a>
</x-app-layout>
