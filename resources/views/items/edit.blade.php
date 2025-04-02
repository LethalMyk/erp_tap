 <x-app-layout>

// Views - resources/views/items/edit.blade.php
    <h1>Editar Item</h1>
    <form action="{{ route('items.update', $item->id) }}" method="POST">
        @csrf @method('PUT')
        <label>Nome:</label>
        <input type="text" name="nomeItem" value="{{ $item->nomeItem }}" required>
        <label>Material:</label>
        <input type="text" name="material" value="{{ $item->material }}" required>
        <label>Metragem:</label>
        <input type="number" name="metragem" value="{{ $item->metragem }}" step="0.01" required>
        <label>Especificações:</label>
        <textarea name="especifi">{{ $item->especifi }}</textarea>
        <button type="submit">Atualizar</button>
    </form>
</x-app-layout>
