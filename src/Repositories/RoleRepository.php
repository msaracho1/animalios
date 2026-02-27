<?php
declare(strict_types=1);

namespace App\Repositories;

final class RoleRepository extends BaseRepository
{
    public function findByName(string $name): ?object
    {
        $row = $this->fetchOne('SELECT * FROM rol WHERE nombre_rol = :n LIMIT 1', ['n' => $name]);
        return $row ? $this->obj($row) : null;
    }


    public function existsById(int $id): bool
    {
        return (bool)$this->fetchOne('SELECT 1 FROM rol WHERE id_rol = :id LIMIT 1', ['id' => $id]);
    }

    public function all(): array
    {
        return $this->objs($this->fetchAll('SELECT * FROM rol ORDER BY nombre_rol'));
    }
}
