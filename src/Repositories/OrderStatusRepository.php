<?php
declare(strict_types=1);

namespace App\Repositories;

final class OrderStatusRepository extends BaseRepository
{
    public function all(): array
    {
        return $this->objs(
            $this->fetchAll(
                'SELECT * FROM estado_pedido ORDER BY id_estado_pedido ASC'
            )
        );
    }
}

