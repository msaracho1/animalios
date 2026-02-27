<?php
declare(strict_types=1);

namespace App\Repositories;

final class OrderHistoryRepository extends BaseRepository
{
    public function create(int $orderId, int $userId, int $estadoId, string $fechaHora): int
    {
        $this->exec(
            'INSERT INTO historial_pedido (id_pedido, id_usuario, id_estado_pedido, fecha_hora)
             VALUES (:p, :u, :e, :f)',
            [
                'p' => $orderId,
                'u' => $userId,
                'e' => $estadoId,
                'f' => $fechaHora,
            ]
        );

        return $this->lastInsertId();
    }

    public function listForOrder(int $orderId): array
    {
        return $this->objs(
            $this->fetchAll(
                'SELECT h.*, ep.nombre_estado AS estado
                 FROM historial_pedido h
                 JOIN estado_pedido ep ON ep.id_estado_pedido = h.id_estado_pedido
                 WHERE h.id_pedido = :id
                 ORDER BY h.fecha_hora DESC',
                ['id' => $orderId]
            )
        );
    }
}