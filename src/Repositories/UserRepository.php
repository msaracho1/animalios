<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;
use RuntimeException;

final class UserRepository extends BaseRepository
{
    public function findWithRole(int $id): ?object
    {
        $row = $this->fetchOne(
            'SELECT u.*, r.id_rol AS role_id_rol, r.nombre_rol AS role_nombre
             FROM usuario u
             LEFT JOIN rol r ON r.id_rol = u.id_rol
             WHERE u.id_usuario = :id
             LIMIT 1',
            ['id' => $id]
        );
        if (!$row) return null;

        $u = $this->obj($row);
        $u->role = (object)[
            'id_rol' => $row['role_id_rol'] ?? null,
            'nombre' => $row['role_nombre'] ?? null,
        ];
        return $u;
    }

    public function findByEmailAndPassword(string $email, string $plainPassword): ?object
    {
        $row = $this->fetchOne('SELECT * FROM usuario WHERE email = :e LIMIT 1', ['e' => $email]);
        if (!$row) {
            return null;
        }

        $storedPassword = (string)($row['contraseña'] ?? '');
        $isBcrypt = str_starts_with($storedPassword, '$2y$')
            || str_starts_with($storedPassword, '$2b$')
            || str_starts_with($storedPassword, '$2a$');

        $valid = false;
        if ($isBcrypt) {
            $valid = password_verify($plainPassword, $storedPassword);
        } else {
            // Compatibilidad con contraseñas legacy en texto plano o SHA1.
            $valid = hash_equals($storedPassword, $plainPassword)
                || hash_equals($storedPassword, sha1($plainPassword));
        }

        return $valid ? $this->obj($row) : null;
    }

    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        $row = $exceptId
            ? $this->fetchOne('SELECT 1 FROM usuario WHERE email = :e AND id_usuario <> :id LIMIT 1', ['e'=>$email,'id'=>$exceptId])
            : $this->fetchOne('SELECT 1 FROM usuario WHERE email = :e LIMIT 1', ['e'=>$email]);

        return (bool)$row;
    }

    public function create(array $data): int
    {
        $this->exec(
            'INSERT INTO usuario (nombre, apellido, fecha_alta, estado, email, contraseña, id_rol)
             VALUES (:nombre,:apellido,:fecha_alta,:estado,:email,:contraseña,:id_rol)',
            [
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'fecha_alta' => $data['fecha_alta'] ?? date('Y-m-d'),
                'estado' => $data['estado'] ?? 1,
                'email' => $data['email'],
                'contraseña' => $data['contraseña'],
                'id_rol' => $data['id_rol'],
            ]
        );
        return $this->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $cols = [];
        $params = ['id' => $id];
        foreach (['nombre','apellido','email','contraseña','id_rol','estado'] as $k) {
            if (!array_key_exists($k, $data)) continue;
            $cols[] = "$k = :$k";
            $params[$k] = $data[$k];
        }
        if (!$cols) return;
        $sql = 'UPDATE usuario SET ' . implode(', ', $cols) . ' WHERE id_usuario = :id';
        $this->exec($sql, $params);
    }


    public function hashForStorage(string $plainPassword): string
    {
        $bcrypt = password_hash($plainPassword, PASSWORD_BCRYPT);
        if ($bcrypt === false) {
            throw new RuntimeException('No se pudo generar hash de contraseña.');
        }

        return $bcrypt;
    }

    public function delete(int $id): void
    {
        $this->exec('DELETE FROM usuario WHERE id_usuario = :id', ['id'=>$id]);
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = max(0, ($page - 1) * $perPage);

        $sql = 'SELECT u.*, r.nombre_rol AS role_nombre, r.id_rol AS role_id_rol
                FROM usuario u
                LEFT JOIN rol r ON r.id_rol = u.id_rol
                ORDER BY u.id_usuario DESC
                LIMIT :lim OFFSET :off';

        $st = $this->pdo()->prepare($sql);
        $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll();

        $countRow = $this->fetchOne('SELECT COUNT(*) c FROM usuario');
        $total = (int)($countRow['c'] ?? 0);

        $users = [];
        foreach ($rows as $row) {
            $u = $this->obj($row);
            $u->role = (object)['id_rol'=>$row['role_id_rol'] ?? null, 'nombre'=>$row['role_nombre'] ?? null];
            $users[] = $u;
        }

        return ['data'=>$users, 'total'=>$total, 'page'=>$page, 'perPage'=>$perPage];
    }

    public function find(int $id): ?object
    {
        $row = $this->fetchOne('SELECT * FROM usuario WHERE id_usuario = :id', ['id'=>$id]);
        return $row ? $this->obj($row) : null;
    }
}
