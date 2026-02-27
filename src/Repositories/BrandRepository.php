<?php
declare(strict_types=1);

namespace App\Repositories;

final class BrandRepository extends BaseRepository
{
    public function all(): array
    {
        return $this->objs($this->fetchAll('SELECT * FROM marca ORDER BY id_marca'));
    }
}
