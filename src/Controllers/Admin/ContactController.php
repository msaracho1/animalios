<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\ContactRepository;
use App\Repositories\ContactStatusRepository;

final class ContactController
{
    public function index(Request $req): void
    {
        $page = max(1, (int)($req->query['page'] ?? 1));
        $contacts = (new ContactRepository())->paginate($page, 15);
        View::render('admin.contacts.index', compact('contacts'));
    }

    public function show(Request $req, string $id): void
    {
        $contact = (new ContactRepository())->findFull((int)$id);
        if (!$contact) {
            http_response_code(404);
            echo 'Consulta no encontrada';
            return;
        }

        $states = (new ContactStatusRepository())->all();
        View::render('admin.contacts.show', compact('contact', 'states'));
    }

    public function updateStatus(Request $req, string $id): void
    {
        $data = $req->validate([
            'id_estado_contacto' => ['required', 'integer'],
        ]);

        $contactRepo = new ContactRepository();
        $contact = $contactRepo->findFull((int)$id);
        if (!$contact) {
            http_response_code(404);
            echo 'Consulta no encontrada';
            return;
        }

        $statusId = (int)$data['id_estado_contacto'];
        $statusRepo = new ContactStatusRepository();
        if (!$statusRepo->existsById($statusId)) {
            Session::flash('error', 'Estado de consulta inválido.');
            Response::redirect(route('admin.contacts.show', ['id' => $id]));
        }

        $contactRepo->updateStatus((int)$id, $statusId);

        Session::flash('success', 'Estado de la consulta actualizado.');
        Response::redirect(route('admin.contacts.show', ['id' => $id]));
    }

    public function respond(Request $req, string $id): void
    {
        $data = $req->validate([
            'respuesta' => ['required', 'string', 'max:2000'],
        ]);

        $contactRepo = new ContactRepository();
        $contact = $contactRepo->findFull((int)$id);
        if (!$contact) {
            http_response_code(404);
            echo 'Consulta no encontrada';
            return;
        }

        $user = Auth::userOrFail();
        $contactRepo->addResponse((int)$id, (int)$user->id_usuario, $data['respuesta'], date('Y-m-d H:i:s'));
        $contactRepo->updateStatus((int)$id, 2);

        Session::flash('success', 'Respuesta registrada correctamente.');
        Response::redirect(route('admin.contacts.show', ['id' => $id]));
    }
}
