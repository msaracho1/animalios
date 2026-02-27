<?php
$title = 'Nosotros - Animalios';

ob_start();
?>

<div class="panel">
  <div class="panel__body">
    <h1 class="page-title">¿Quiénes somos?</h1>
    <p class="muted" style="max-width:900px; line-height:1.6">
      Animalios es una tienda demo (proyecto académico) hecha en PHP vanilla. La idea es simular una experiencia simple de e‑commerce
      con catálogo, carrito, checkout, pedidos y panel admin.
    </p>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/app.php';
