<?php

namespace App\Repositories;

use App\Models\Item;

class ItemRepository
{
    public function all()
    {
        return Item::with('terceirizadas')->get();
    }

    public function find($id)
    {
        return Item::with('terceirizadas')->find($id);
    }

    public function create(array $dados)
    {
        return Item::create($dados);
    }

    public function update(Item $item, array $dados)
    {
        return $item->update($dados);
    }

    public function delete(Item $item)
    {
        return $item->delete();
    }
}
