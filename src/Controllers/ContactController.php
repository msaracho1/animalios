<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\ContactRepository;

final class ContactController
{
    public function store(Request $req): void
    {
        $data = $req->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'asunto' => ['required', 'string', 'max:150'],
            'mensaje' => ['required', 'string', 'max:2000'],
            'prioridad' => ['required', 'string'],
        ]);

        $prioridad = in_array($data['prioridad'], ['baja', 'media', 'alta'], true)
            ? $data['prioridad']
            : 'media';

        $ticket = 'ANI-' . date('YmdHis') . '-' . random_int(100, 999);
        $user = Auth::user();

        (new ContactRepository())->create([
            'numero_ticket' => $ticket,
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'asunto' => $data['asunto'],
            'mensaje' => $data['mensaje'],
            'fecha_creacion' => date('Y-m-d H:i:s'),
            'prioridad' => $prioridad,
            'id_usuario' => $user?->id_usuario,
            'id_estado_contacto' => 1,
        ]);

        Session::flash('success', 'Gracias por contactarte. Tu ticket es ' . $ticket . '.');
        Response::redirect(route('about'));
    }
}
