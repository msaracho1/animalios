<?php
declare(strict_types=1);

namespace App\Repositories;

final class OrderItemRepository extends BaseRepository
{
    public function create(int $orderId, int $productId, int $cantidad, float $precioUnitario): int
    {
        $subtotal = $cantidad * $precioUnitario;

        $this->exec(
            'INSERT INTO detalle_pedido (id_pedido, id_producto, cantidad, precio_unitario, subtotal)
             VALUES (:p, :prod, :c, :pu, :s)',
            [
                'p'    => $orderId,
                'prod' => $productId,
                'c'    => $cantidad,
                'pu'   => $precioUnitario,
                's'    => $subtotal,
            ]
        );

        return $this->lastInsertId();
    }

    public function listForOrder(int $orderId): array
    {
        $rows = $this->fetchAll(
            'SELECT d.*, pr.nombre AS prod_nombre
             FROM detalle_pedido d
             LEFT JOIN producto pr ON pr.id_producto = d.id_producto
             WHERE d.id_pedido = :id
             ORDER BY d.id_detalle_pedido ASC',
            ['id' => $orderId]
        );

        $items = [];
        foreach ($rows as $row) {
            $i = $this->obj($row);
            $i->product = (object)[
                'nombre'      => $row['prod_nombre'] ?? null,
                'id_producto' => $row['id_producto'] ?? null
            ];
            $items[] = $i;
        }

        return $items;
    }
}