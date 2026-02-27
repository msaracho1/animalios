<?php
declare(strict_types=1);

namespace App\Repositories;

final class CategoryRepository extends BaseRepository
{
    public function all(): array
    {
        return $this->objs($this->fetchAll('SELECT * FROM categoria ORDER BY nombre_categoria'));
    }
}
