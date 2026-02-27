<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use App\Repositories\UserRepository;

final class ProfileController
{
    public function edit(Request $req): void
    {
        $user = Auth::userOrFail();
        View::render('profile.edit', compact('user'));
    }

    public function update(Request $req): void
    {
        $user = Auth::userOrFail();

        $data = $req->validate([
            'nombre' => ['required','string','max:45'],
            'email' => ['required','email','max:150'],
        ]);

        $repo = new UserRepository();
        if ($repo->emailExists($data['email'], (int)$user->id_usuario)) {
            Session::flash('error', 'Ese email ya está en uso.');
            Response::back();
        }

        $repo->update((int)$user->id_usuario, $data);
        Session::flash('success', 'Perfil actualizado.');
        Response::back();
    }

    public function updatePassword(Request $req): void
    {
        $user = Auth::userOrFail();
        $data = $req->validate([
            'contraseña' => ['required','string','min:4','max:255'],
        ]);

        $repo = new UserRepository();
        $repo->update((int)$user->id_usuario, ['contraseña' => $repo->hashForStorage($data['contraseña'])]);
        Session::flash('success', 'Contraseña actualizada.');
        Response::back();
    }
}
