<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'Animalios', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= htmlspecialchars(base_path() . '/assets/css/app.css', ENT_QUOTES, 'UTF-8') ?>">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?= htmlspecialchars(base_path() . '/assets/css/app.css', ENT_QUOTES, 'UTF-8') ?>">
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<body>

<?php
  $user = auth()->user();
  $isAdmin = $user && isset($user->role) && in_array(($user->role->nombre ?? null), ['admin', 'administrador'], true);
  $isSeller = $user && (($user->role->nombre ?? null) === 'vendedor');
  $cart = \App\Core\Session::get('cart', []);
  $cartCount = 0;
  foreach ($cart as $it) { $cartCount += (int)($it['qty'] ?? 0); }
?>

<div class="topbar">
  <div class="wrap">
    ENVÍOS GRATIS EN CABA Y GBA DESDE $27000
  </div>
</div>

<header class="header">
  <div class="wrap header__inner">
    <a class="brand" href="<?= htmlspecialchars(route('home'), ENT_QUOTES, 'UTF-8') ?>" aria-label="Animalios">
      <span class="brand__name">ANIMALIOS</span>
    </a>

    <nav class="nav" aria-label="principal">
      <a class="nav__link" href="<?= htmlspecialchars(route('store.index'), ENT_QUOTES, 'UTF-8') ?>">Tienda</a>
      <a class="nav__link" href="<?= htmlspecialchars(route('about'), ENT_QUOTES, 'UTF-8') ?>">Nosotros</a>

      <?php if (\App\Core\Auth::check()): ?>
        <a class="nav__link" href="<?= htmlspecialchars(route('orders.index'), ENT_QUOTES, 'UTF-8') ?>">Pedidos</a>

        <?php if ($isSeller): ?>
          <a class="nav__link" href="<?= htmlspecialchars(route('vendor.orders.index'), ENT_QUOTES, 'UTF-8') ?>">Pedidos Vendedor</a>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
          <a class="nav__link" href="<?= htmlspecialchars(route('admin.users.index'), ENT_QUOTES, 'UTF-8') ?>">Usuarios</a>
          <a class="nav__link" href="<?= htmlspecialchars(route('admin.products.index'), ENT_QUOTES, 'UTF-8') ?>">Productos</a>
          <a class="nav__link" href="<?= htmlspecialchars(route('admin.orders.index'), ENT_QUOTES, 'UTF-8') ?>">Pedidos Admin</a>
        <?php endif; ?>
      <?php endif; ?>
    </nav>

    <div class="header__actions">

      <?php if (\App\Core\Auth::check()): ?>

        <!-- Perfil -->
        <a class="iconlink" 
           href="<?= htmlspecialchars(route('profile.edit'), ENT_QUOTES, 'UTF-8') ?>" 
           title="Perfil" 
           aria-label="Perfil">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 21a8 8 0 0 0-16 0"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
        </a>

      <?php else: ?>

        <!-- Login / Registro visibles -->
        <a class="btn btn--ghost" 
           href="<?= htmlspecialchars(route('login'), ENT_QUOTES, 'UTF-8') ?>">
           Iniciar sesión
        </a>

        <a class="btn" 
           href="<?= htmlspecialchars(route('register'), ENT_QUOTES, 'UTF-8') ?>">
           Registrarse
        </a>

      <?php endif; ?>

      <!-- Carrito -->
      <a class="iconlink" 
         href="<?= htmlspecialchars(route('cart.index'), ENT_QUOTES, 'UTF-8') ?>" 
         title="Carrito" 
         aria-label="Carrito">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"/>
          <circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>
        </svg>

        <?php if ($cartCount > 0): ?>
          <span class="badge" aria-label="<?= (int)$cartCount ?> items en el carrito">
            <?= (int)$cartCount ?>
          </span>
        <?php endif; ?>
      </a>

      <?php if (\App\Core\Auth::check()): ?>
        <form method="POST" 
              action="<?= htmlspecialchars(route('logout'), ENT_QUOTES, 'UTF-8') ?>" 
              class="logout">
          <input type="hidden" 
                 name="_token" 
                 value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
          <button class="btn btn--ghost" type="submit">Salir</button>
        </form>
      <?php endif; ?>

    </div>
  </div>
</header>

<main class="main">
  <div class="wrap">
    <?php if (session('success')): ?>
      <div class="flash flash--success"><?= htmlspecialchars((string)session('success'), ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

<?php
  $err = session('error');
  if (is_array($err)) {
      $err = implode(' · ', array_map('strval', $err));
  }

  $errText = trim((string)$err);
?>

<?php if ($errText !== ''): ?>
  <div class="flash flash--error"><?= htmlspecialchars($errText, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

    <?= $content ?? '' ?>
  </div>
</main>

<footer class="footer">
  <div class="wrap footer__inner">
    <div class="footer__brand"><span class="brand__name">ANIMALIOS</span></div>
    <div class="footer__copy">© Código en progreso 2024 · Todos los derechos reservados</div>
  </div>
</footer>

</body>
</html>
