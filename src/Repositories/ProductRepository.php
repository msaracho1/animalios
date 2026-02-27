<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ProductRepository extends BaseRepository
{
    public function featured(int $limit = 8): array
    {
        // ✅ No asumimos columnas inexistentes en marca/subcategoria/categoria
        $sql = 'SELECT p.*
                FROM producto p
                ORDER BY p.id_producto DESC
                LIMIT :lim';

        $st = $this->pdo()->prepare($sql);
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll();

        return array_map(function($row) {
            $p = $this->obj($row);
            // relaciones null por ahora (hasta confirmar columnas reales)
            $p->brand = (object)['nombre' => null];
            $p->subcategory = (object)['nombre' => null, 'category' => (object)['nombre' => null]];
            return $p;
        }, $rows);
    }

    public function findWithRelations(int $id): ?object
    {
        $row = $this->fetchOne(
            'SELECT p.*
             FROM producto p
             WHERE p.id_producto = :id
             LIMIT 1',
            ['id'=>$id]
        );

        if (!$row) return null;

        $p = $this->obj($row);
        $p->brand = (object)['nombre' => null];
        $p->subcategory = (object)['nombre' => null, 'category' => (object)['nombre' => null]];
        return $p;
    }

    public function search(array $filters, int $page, int $perPage): array
    {
        $where = [];
        $params = [];

        // ✅ Filtros que sí existen en producto
        if (!empty($filters['id_subcategoria'])) {
            $where[] = 'p.id_subcategoria = :id_subcategoria';
            $params['id_subcategoria'] = (int)$filters['id_subcategoria'];
        }
        if (!empty($filters['id_marca'])) {
            $where[] = 'p.id_marca = :id_marca';
            $params['id_marca'] = (int)$filters['id_marca'];
        }
        if (!empty($filters['q'])) {
            $where[] = 'p.nombre LIKE :q';
            $params['q'] = '%' . trim((string)$filters['q']) . '%';
        }

        // ⚠️ id_categoria depende de JOIN con subcategoria/categoria (lo dejamos fuera por ahora)
        // Si lo necesitás ya, pasame las columnas reales de subcategoria/categoria y lo reactivamos.

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
        $offset = max(0, ($page - 1) * $perPage);

        $sql = "SELECT p.*
                FROM producto p
                $whereSql
                ORDER BY p.id_producto DESC
                LIMIT :lim OFFSET :off";

        $st = $this->pdo()->prepare($sql);
        foreach ($params as $k=>$v) {
            $st->bindValue(':' . $k, $v);
        }
        $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll();

        $countSql = "SELECT COUNT(*) c
                     FROM producto p
                     $whereSql";

        $count = $this->fetchOne($countSql, $params);
        $total = (int)($count['c'] ?? 0);

        $products = array_map(function($row) {
            $p = $this->obj($row);
            $p->brand = (object)['nombre' => null];
            $p->subcategory = (object)['nombre' => null, 'category' => (object)['nombre' => null]];
            return $p;
        }, $rows);

        return ['data'=>$products, 'total'=>$total, 'page'=>$page, 'perPage'=>$perPage];
    }

    public function create(array $data): int
    {
        $this->exec(
            'INSERT INTO producto (nombre, descripcion, precio, stock, id_marca, id_subcategoria)
             VALUES (:nombre,:descripcion,:precio,:stock,:id_marca,:id_subcategoria)',
            [
                'nombre'=>$data['nombre'],
                'descripcion'=>$data['descripcion'] ?? null,
                'precio'=>$data['precio'],
                'stock'=>$data['stock'],
                'id_marca'=>$data['id_marca'],
                'id_subcategoria'=>$data['id_subcategoria'],
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->exec(
            'UPDATE producto
             SET nombre=:nombre, descripcion=:descripcion, precio=:precio, stock=:stock, id_marca=:id_marca, id_subcategoria=:id_subcategoria
             WHERE id_producto = :id',
            [
                'id'=>$id,
                'nombre'=>$data['nombre'],
                'descripcion'=>$data['descripcion'] ?? null,
                'precio'=>$data['precio'],
                'stock'=>$data['stock'],
                'id_marca'=>$data['id_marca'],
                'id_subcategoria'=>$data['id_subcategoria'],
            ]
        );
    }

    public function delete(int $id): void
    {
        $this->exec('DELETE FROM producto WHERE id_producto = :id', ['id'=>$id]);
    }

    public function find(int $id): ?object
    {
        $row = $this->fetchOne('SELECT * FROM producto WHERE id_producto = :id', ['id'=>$id]);
        return $row ? $this->obj($row) : null;
    }
}