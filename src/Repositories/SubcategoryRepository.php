<?php
declare(strict_types=1);

namespace App\Repositories;

final class SubcategoryRepository extends BaseRepository
{
    public function all(): array
    {
        return $this->objs($this->fetchAll('SELECT * FROM subcategoria ORDER BY nombre_subcategoria'));
    }
}
