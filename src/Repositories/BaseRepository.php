<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\DB;
use PDO;

abstract class BaseRepository
{
    protected function pdo(): PDO { return DB::pdo(); }

    protected function fetchAll(string $sql, array $params = []): array
    {
        $st = $this->pdo()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $st = $this->pdo()->prepare($sql);
        $st->execute($params);
        $row = $st->fetch();
        return $row === false ? null : $row;
    }

    protected function exec(string $sql, array $params = []): int
    {
        $st = $this->pdo()->prepare($sql);
        $st->execute($params);
        return $st->rowCount();
    }

    protected function lastInsertId(): int
    {
        return (int)$this->pdo()->lastInsertId();
    }

    protected function obj(array $row): object
    {
        return json_decode(json_encode($row), false, 512, JSON_THROW_ON_ERROR);
    }

    protected function objs(array $rows): array
    {
        return array_map(fn($r) => $this->obj($r), $rows);
    }
}
