<?php
declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class ContactRepository extends BaseRepository
{
    public function create(array $data): int
    {
        $this->exec(
            'INSERT INTO contacto (numero_ticket, nombre, email, asunto, mensaje, fecha_creacion, prioridad, id_usuario, id_estado_contacto)
             VALUES (:numero_ticket, :nombre, :email, :asunto, :mensaje, :fecha_creacion, :prioridad, :id_usuario, :id_estado_contacto)',
            [
                'numero_ticket' => $data['numero_ticket'],
                'nombre' => $data['nombre'],
                'email' => $data['email'],
                'asunto' => $data['asunto'],
                'mensaje' => $data['mensaje'],
                'fecha_creacion' => $data['fecha_creacion'],
                'prioridad' => $data['prioridad'],
                'id_usuario' => $data['id_usuario'],
                'id_estado_contacto' => $data['id_estado_contacto'],
            ]
        );

        return $this->lastInsertId();
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = max(0, ($page - 1) * $perPage);

        $sql = 'SELECT c.*, ec.nombre_estado
                FROM contacto c
                JOIN estado_contacto ec ON ec.id_estado_contacto = c.id_estado_contacto
                ORDER BY c.fecha_creacion DESC
                LIMIT :lim OFFSET :off';

        $st = $this->pdo()->prepare($sql);
        $st->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $st->bindValue(':off', $offset, PDO::PARAM_INT);
        $st->execute();

        $rows = $st->fetchAll();
        $count = $this->fetchOne('SELECT COUNT(*) c FROM contacto');

        return [
            'data' => $this->objs($rows),
            'total' => (int)($count['c'] ?? 0),
            'page' => $page,
            'perPage' => $perPage,
        ];
    }

    public function findFull(int $id): ?object
    {
        $row = $this->fetchOne(
            'SELECT c.*, ec.nombre_estado
             FROM contacto c
             JOIN estado_contacto ec ON ec.id_estado_contacto = c.id_estado_contacto
             WHERE c.id_contacto = :id
             LIMIT 1',
            ['id' => $id]
        );

        if (!$row) {
            return null;
        }

        $contact = $this->obj($row);
        $contact->responses = $this->objs(
            $this->fetchAll(
                'SELECT r.*, u.nombre, u.apellido
                 FROM respuesta_contacto r
                 JOIN usuario u ON u.id_usuario = r.id_usuario
                 WHERE r.id_contacto = :id
                 ORDER BY r.fecha_respuesta ASC',
                ['id' => $id]
            )
        );

        return $contact;
    }

    public function updateStatus(int $id, int $statusId): void
    {
        $this->exec(
            'UPDATE contacto SET id_estado_contacto = :status WHERE id_contacto = :id',
            ['status' => $statusId, 'id' => $id]
        );
    }

    public function addResponse(int $contactId, int $userId, string $response, string $date): int
    {
        $this->exec(
            'INSERT INTO respuesta_contacto (id_contacto, id_usuario, fecha_respuesta, respuesta)
             VALUES (:contacto, :usuario, :fecha, :respuesta)',
            [
                'contacto' => $contactId,
                'usuario' => $userId,
                'fecha' => $date,
                'respuesta' => $response,
            ]
        );

        return $this->lastInsertId();
    }
}
