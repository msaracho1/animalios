<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

final class UserController
{
    public function index(Request $req): void
    {
        $page = max(1, (int)($req->query['page'] ?? 1));
        $users = (new UserRepository())->paginate($page, 15);
        View::render('admin.users.index', compact('users'));
    }

    public function create(Request $req): void
    {
        $roles = (new RoleRepository())->all();
        View::render('admin.users.create', compact('roles'));
    }

    public function store(Request $req): void
    {
        $data = $req->validate([
            'nombre' => ['required','string','max:45'],
            'apellido' => ['required','string','max:45'],
            'email' => ['required','email','max:150'],
            'contraseña' => ['required','string','min:4','max:255'],
            'id_rol' => ['required','integer'],
        ]);

        $repo = new UserRepository();
        if ($repo->emailExists($data['email'])) {
            Session::flash('error', 'Ese email ya está registrado.');
            Response::back();
        }

        $data['contraseña'] = $repo->hashForStorage($data['contraseña']);
        $data['fecha_alta'] = date('Y-m-d');
        $data['estado'] = 1;
        $repo->create($data);

        Session::flash('success', 'Usuario creado.');
        Response::redirect(route('admin.users.index'));
    }

    public function edit(Request $req, string $id): void
    {
        $user = (new UserRepository())->find((int)$id);
        if (!$user) {
            http_response_code(404);
            echo 'Usuario no encontrado';
            return;
        }
        $roles = (new RoleRepository())->all();
        View::render('admin.users.edit', compact('user','roles'));
    }

    public function update(Request $req, string $id): void
    {
        $userId = (int)$id;
        $repo = new UserRepository();
        $user = $repo->find($userId);
        if (!$user) {
            http_response_code(404);
            echo 'Usuario no encontrado';
            return;
        }

        $data = $req->validate([
            'nombre' => ['required','string','max:45'],
            'apellido' => ['required','string','max:45'],
            'email' => ['required','email','max:150'],
            'id_rol' => ['required','integer'],
            'contraseña' => ['nullable','string','min:4','max:255'],
        ]);

        if ($repo->emailExists($data['email'], $userId)) {
            Session::flash('error', 'Ese email ya está en uso.');
            Response::back();
        }

        if (!empty($data['contraseña'])) {
            $data['contraseña'] = $repo->hashForStorage($data['contraseña']);
        } else {
            unset($data['contraseña']);
        }

        $repo->update($userId, $data);
        Session::flash('success', 'Usuario actualizado.');
        Response::redirect(route('admin.users.index'));
    }

    public function destroy(Request $req, string $id): void
    {
        $targetId = (int)$id;
        $me = Auth::userOrFail();

        if ((int)$me->id_usuario === $targetId) {
            Session::flash('error', 'No podés darte de baja a vos mismo.');
            Response::redirect(route('admin.users.index'));
        }

        (new UserRepository())->delete($targetId);
        Session::flash('success', 'Usuario eliminado.');
        Response::redirect(route('admin.users.index'));
    }
}
