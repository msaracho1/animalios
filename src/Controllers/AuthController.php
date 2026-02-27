<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;

final class AuthController
{
    public function showLogin(Request $req): void
    {
        View::render('auth.login');
    }

    public function login(Request $req): void
    {
        $data = $req->validate([
            'email' => ['required','email'],
            'contraseña' => ['required','string'],
        ]);

        $user = (new UserRepository())->findByEmailAndPassword($data['email'], $data['contraseña']);
        if (!$user) {
            Session::flash('error', 'Credenciales inválidas.');
            Response::back();
        }

        Auth::login((int)$user->id_usuario);
        $userFull = Auth::user();

        if (in_array(($userFull->role->nombre ?? null), ['admin','administrador'], true)) {
            Response::redirect(route('admin.products.index'));
        }

        Response::redirect(route('orders.index'));
    }

    public function showRegister(Request $req): void
    {
        View::render('auth.register');
    }

    public function register(Request $req): void
    {
        $data = $req->validate([
            'nombre' => ['required','string','max:45'],
            'apellido' => ['required','string','max:45'],
            'email' => ['required','email','max:150'],
            'contraseña' => ['required','string','min:4','max:255'],
        ]);

        $users = new UserRepository();
        if ($users->emailExists($data['email'])) {
            Session::flash('error', 'Ese email ya está registrado.');
            Response::back();
        }

        $roleCliente = (new RoleRepository())->findByName('cliente');
        if (!$roleCliente) {
            Session::flash('error', 'No existe el rol cliente en la base.');
            Response::back();
        }

        $id = $users->create([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'email' => $data['email'],
            'contraseña' => $users->hashForStorage($data['contraseña']),
            'id_rol' => (int)$roleCliente->id_rol,
            'fecha_alta' => date('Y-m-d'),
            'estado' => 1,
        ]);

        Auth::login($id);
        Session::flash('success', 'Cuenta creada correctamente.');
        Response::redirect(route('orders.index'));
    }

    public function logout(Request $req): void
    {
        Auth::logout();
        Session::flash('success', 'Sesión cerrada.');
        Response::redirect(route('home'));
    }
}
