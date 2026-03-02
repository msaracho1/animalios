<?php
declare(strict_types=1);

namespace App\Repositories;

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
}
