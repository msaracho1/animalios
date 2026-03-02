<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class OrderRepository extends BaseRepository
{
    public function create(int $userId, string $fecha, float $total): int
    {
        $this->exec(
            'INSERT INTO pedido (id_usuario, fecha_creacion, total, id_estado_pedido, direccion_envio, metodo_pago, observaciones)
             VALUES (:u, :f, :t, :e, :d, :m, :o)',
            [
                'u' => $userId,
                'f' => $fecha,
                't' => $total,
                'e' => 1,
                'd' => 'Sin dirección especificada',
                'm' => 'No definido',
                'o' => '',
            ]
        );

        return $this->lastInsertId();
    }

    public function find(int $id): ?object
    {
        $row = $this->fetchOne('SELECT * FROM pedido WHERE id_pedido = :id', ['id'=>$id]);
        return $row ? $this->obj($row) : null;
    }

    public function listForUser(int $userId): array
    {
        $orders = $this->objs($this->fetchAll('SELECT * FROM pedido WHERE id_usuario = :u ORDER BY fecha_creacion DESC', ['u'=>$userId]));
        // attach history
        $histRepo = new OrderHistoryRepository();
        foreach ($orders as $o) {
            $o->history = $histRepo->listForOrder((int)$o->id_pedido);
        }
        return $orders;
    }

    public function findWithItemsAndHistory(int $orderId): ?object
    {
        $row = $this->fetchOne('SELECT * FROM pedido WHERE id_pedido = :id', ['id'=>$orderId]);
        if (!$row) return null;
        $o = $this->obj($row);

        $itemRepo = new OrderItemRepository();
        $histRepo = new OrderHistoryRepository();
        $o->items = $itemRepo->listForOrder((int)$o->id_pedido);
        $o->history = $histRepo->listForOrder((int)$o->id_pedido);
        return $o;
    }

    public function paginateAdmin(array $filters, int $page, int $perPage): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['dias'])) {
            $dias = (int)$filters['dias'];
            if ($dias > 0 && $dias <= 365) {
                $where[] = 'p.fecha_creacion >= (NOW() - INTERVAL :dias DAY)';
                $params['dias'] = $dias;
            }
        }

        // estado: match any history row with that state (simple, like original)
        if (!empty($filters['estado'])) {
            $where[] = 'p.id_estado_pedido = :estado';
            $params['estado'] = (int)$filters['estado'];
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $offset = max(0, ($page-1)*$perPage);

        $sql = "SELECT p.*, u.nombre AS user_nombre, u.email AS user_email, ep.nombre_estado AS estado_nombre
                FROM pedido p
                LEFT JOIN usuario u ON u.id_usuario = p.id_usuario
                LEFT JOIN estado_pedido ep ON ep.id_estado_pedido = p.id_estado_pedido
                $whereSql
                ORDER BY p.fecha_creacion DESC
                LIMIT :lim OFFSET :off";

        $st = $this->pdo()->prepare($sql);
        foreach ($params as $k=>$v) {
            $st->bindValue(':' . $k, $v);
        }
        $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll();

        $countRow = $this->fetchOne("SELECT COUNT(*) c FROM pedido p $whereSql", $params);
        $total = (int)($countRow['c'] ?? 0);

        $histRepo = new OrderHistoryRepository();
        $orders = [];
        foreach ($rows as $row) {
            $o = $this->obj($row);
            $o->user = (object)['nombre'=>$row['user_nombre'] ?? null, 'email'=>$row['user_email'] ?? null];
            $o->estado_nombre = $row['estado_nombre'] ?? null;
            $o->history = $histRepo->listForOrder((int)$o->id_pedido);
            $orders[] = $o;
        }

        return ['data'=>$orders,'total'=>$total,'page'=>$page,'perPage'=>$perPage];
    }

    public function findAdminFull(int $orderId): ?object
    {
        $row = $this->fetchOne('SELECT p.*, u.nombre AS user_nombre, u.email AS user_email, ep.nombre_estado AS estado_nombre
                                FROM pedido p
                                LEFT JOIN usuario u ON u.id_usuario = p.id_usuario
                                LEFT JOIN estado_pedido ep ON ep.id_estado_pedido = p.id_estado_pedido
                                WHERE p.id_pedido = :id
                                LIMIT 1', ['id'=>$orderId]);
        if (!$row) return null;
        $o = $this->obj($row);
        $o->user = (object)['nombre'=>$row['user_nombre'] ?? null, 'email'=>$row['user_email'] ?? null];
        $o->estado_nombre = $row['estado_nombre'] ?? null;

        $itemRepo = new OrderItemRepository();
        $histRepo = new OrderHistoryRepository();
        $o->items = $itemRepo->listForOrder((int)$o->id_pedido);
        $o->history = $histRepo->listForOrder((int)$o->id_pedido);
        return $o;
    }

    public function updateStatus(int $orderId, int $statusId): void
    {
        $this->exec(
            'UPDATE pedido SET id_estado_pedido = :estado WHERE id_pedido = :id',
            ['estado' => $statusId, 'id' => $orderId]
        );
    }
}
