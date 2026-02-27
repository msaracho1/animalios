<?php
/** @var object $user */
/** @var array $roles */
$title = 'Editar usuario - Animalios';

ob_start();
?>

<div class="panel">
  <div class="panel__body">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
      <h1 class="page-title" style="margin:0;">Editar usuario #<?= (int)$user->id_usuario ?></h1>
      <a class="btn btn--sm" href="<?= htmlspecialchars(route('admin.users.index'), ENT_QUOTES, 'UTF-8') ?>">← Volver</a>
    </div>

    <form method="POST" action="<?= htmlspecialchars(route('admin.users.update', ['id' => $user->id_usuario]), ENT_QUOTES, 'UTF-8') ?>" class="form" style="margin:14px 0 0; max-width:720px;">
      <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">

      <div class="form__row">
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="<?= htmlspecialchars((string)old('nombre', $user->nombre), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="form__row">
        <label for="apellido">Apellido</label>
        <input id="apellido" name="apellido" value="<?= htmlspecialchars((string)old('apellido', $user->apellido), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="form__row">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="<?= htmlspecialchars((string)old('email', $user->email), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="form__row">
        <label for="rol">Rol</label>
        <select id="rol" name="id_rol" required>
          <?php foreach ($roles as $r): ?>
            <option value="<?= (int)$r->id_rol ?>" <?= ((int)$r->id_rol === (int)$user->id_rol) ? 'selected' : '' ?>>
              <?= htmlspecialchars((string)$r->nombre, ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form__row">
        <label for="pass">Nueva contraseña (opcional)</label>
        <input id="pass" name="contraseña" type="password" placeholder="Dejar vacío para no cambiar">
      </div>

      <div class="form__actions" style="justify-content:flex-end;">
        <button class="btn btn--primary" type="submit">Actualizar</button>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
