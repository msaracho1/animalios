<?php
$title = $title ?? 'URL no encontrada';
$statusCode = $statusCode ?? 404;
$message = $message ?? 'La URL solicitada no existe o no tenés permisos para acceder.';

ob_start();
?>
<section style="max-width:760px;margin:48px auto;text-align:center;">
  <p class="muted" style="letter-spacing:1px;font-weight:700;"><?= (int)$statusCode ?></p>
  <h1 style="margin-bottom:12px;"><?= htmlspecialchars((string)$title, ENT_QUOTES, 'UTF-8') ?></h1>
  <p class="muted" style="margin-bottom:20px;"><?= htmlspecialchars((string)$message, ENT_QUOTES, 'UTF-8') ?></p>
  <a class="btn" href="<?= htmlspecialchars(route('home'), ENT_QUOTES, 'UTF-8') ?>">Volver al inicio</a>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
