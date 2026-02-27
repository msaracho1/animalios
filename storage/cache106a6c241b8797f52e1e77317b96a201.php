<?php
?>
<?php
/** @var string|null $title */
$title = $title ?? 'Animalios';

ob_start();
?>
  <h1>Home</h1>
  <p>Bienvenida a Animalios 👋</p>

  <p>
    Ir a la <a href="<?= htmlspecialchars(route('store.index'), ENT_QUOTES, 'UTF-8') ?>">tienda</a>.
  </p>
<?php
$content = ob_get_clean();

require __DIR__ . '/layouts/app.php';