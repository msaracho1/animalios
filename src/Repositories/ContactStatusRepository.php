<?php
declare(strict_types=1);

namespace App\Repositories;

final class ContactStatusRepository extends BaseRepository
{
    public function all(): array
    {
        return $this->objs(
            $this->fetchAll('SELECT * FROM estado_contacto ORDER BY id_estado_contacto ASC')
        );
    }

    public function existsById(int $id): bool
    {
        return (bool)$this->fetchOne(
            'SELECT 1 FROM estado_contacto WHERE id_estado_contacto = :id LIMIT 1',
            ['id' => $id]
        );
    }
}
