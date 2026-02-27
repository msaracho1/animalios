<?php
$title = 'Registro - Animalios';

ob_start();
?>

<div class="form">
  <div class="panel">
    <div class="panel__body">
      <h1 class="page-title" style="text-align:center">Registrate y conocé Animalios</h1>

      <form method="POST" action="<?= htmlspecialchars(route('register.post'), ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">

        <div class="form__row">
          <label for="nombre">Nombre</label>
          <input id="nombre" name="nombre" value="<?= htmlspecialchars((string)old('nombre'), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form__row">
          <label for="apellido">Apellido</label>
          <input id="apellido" name="apellido" value="<?= htmlspecialchars((string)old('apellido'), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form__row">
          <label for="email">Correo electrónico</label>
          <input id="email" type="email" name="email" value="<?= htmlspecialchars((string)old('email'), ENT_QUOTES, 'UTF-8') ?>" autocomplete="email" required>
        </div>

        <div class="form__row">
          <label for="pass">Contraseña</label>
          <input id="pass" type="password" name="contraseña" autocomplete="new-password" required>
        </div>

        <div class="form__actions">
          <button class="btn btn--primary" type="submit">Registrarse</button>
        </div>
      </form>

      <p class="muted" style="text-align:center; margin:14px 0 0">
        ¿Ya tenés una cuenta?
        <a href="<?= htmlspecialchars(route('login'), ENT_QUOTES, 'UTF-8') ?>" style="color:var(--accent); font-weight:800">Iniciá sesión</a>
      </p>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
