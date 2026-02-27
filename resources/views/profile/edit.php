<?php
/** @var object $user */
$title = 'Mi perfil - Animalios';

ob_start();
?>

<div class="store-layout">
  <aside class="sidebar" aria-label="perfil">
    <div style="display:flex; gap:12px; align-items:center; margin-bottom:12px;">
      <div style="width:44px; height:44px; border-radius:999px; background:rgba(0,0,0,.15);"></div>
      <div>
        <div style="font-weight:900">Hola,</div>
        <div class="muted" style="font-weight:800; font-size:12px;">
          <?= htmlspecialchars((string)$user->nombre, ENT_QUOTES, 'UTF-8') ?>
        </div>
      </div>
    </div>
    <a href="<?= htmlspecialchars(route('profile.edit'), ENT_QUOTES, 'UTF-8') ?>">Perfil</a>
    <a href="<?= htmlspecialchars(route('orders.index'), ENT_QUOTES, 'UTF-8') ?>">Pedidos</a>
    <a href="<?= htmlspecialchars(route('cart.index'), ENT_QUOTES, 'UTF-8') ?>">Carrito</a>
  </aside>

  <section>
    <h1 class="page-title">Mi Perfil</h1>

    <div class="panel">
      <div class="panel__body">
        <h2 class="section-title" style="text-align:left; margin:0 0 10px">Datos</h2>
        <form method="POST" action="<?= htmlspecialchars(route('profile.update'), ENT_QUOTES, 'UTF-8') ?>" class="form" style="max-width:none; margin:0;">
          <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">

          <div class="form__row">
            <label for="nombre">Nombre</label>
            <input id="nombre" name="nombre" value="<?= htmlspecialchars((string)old('nombre', $user->nombre), ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="form__row">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="<?= htmlspecialchars((string)old('email', $user->email), ENT_QUOTES, 'UTF-8') ?>" required>
          </div>

          <div class="form__actions" style="justify-content:flex-end">
            <button class="btn btn--primary" type="submit">Guardar cambios</button>
          </div>
        </form>
      </div>
    </div>

    <div class="panel" style="margin-top:16px;">
      <div class="panel__body">
        <h2 class="section-title" style="text-align:left; margin:0 0 10px">Cambiar contraseña</h2>
        <form method="POST" action="<?= htmlspecialchars(route('profile.password'), ENT_QUOTES, 'UTF-8') ?>" class="form" style="max-width:none; margin:0;">
          <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">

          <div class="form__row">
            <label for="pass">Nueva contraseña</label>
            <input id="pass" type="password" name="contraseña" required>
          </div>

          <div class="form__actions" style="justify-content:flex-end">
            <button class="btn" type="submit">Actualizar contraseña</button>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
