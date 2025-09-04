<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Terceirizada;
use App\Repositories\ItemRepository;

class ItemService
{
    protected $repository;

    public function __construct(ItemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listarTodos()
    {
        return $this->repository->all();
    }

    public function buscarPorId($id)
    {
        return $this->repository->find($id);
    }

    public function criar(array $dados)
    {
        $item = $this->repository->create($dados);

        // Se vierem terceirizadas, cadastra junto
        if (!empty($dados['terceirizadas'])) {
            foreach ($dados['terceirizadas'] as $terc) {
                $item->terceirizadas()->create([
                    'tipoServico' => $terc['tipo'] ?? '',
                    'obs' => $terc['obs'] ?? '',
                ]);
            }
        }

        return $item;
    }

    public function atualizar(Item $item, array $dados)
    {
        $this->repository->update($item, $dados);

        // Atualiza ou cria terceirizadas
        if (!empty($dados['terceirizadas'])) {
            foreach ($dados['terceirizadas'] as $tercData) {
                if (!empty($tercData['id'])) {
                    $terc = Terceirizada::find($tercData['id']);
                    if ($terc && $terc->item_id == $item->id) {
                        $terc->update([
                            'tipoServico' => $tercData['tipoServico'] ?? '',
                            'obs' => $tercData['obs'] ?? '',
                        ]);
                    }
                } else {
                    // Cria nova terceirizada
                    $item->terceirizadas()->create([
                        'tipoServico' => $tercData['tipoServico'] ?? '',
                        'obs' => $tercData['obs'] ?? '',
                    ]);
                }
            }
        }

        return $item;
    }

    public function deletar(Item $item)
    {
        return $this->repository->delete($item);
    }
}
