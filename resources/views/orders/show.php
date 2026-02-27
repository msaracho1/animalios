<?php
/** @var object $order */
$title = 'Pedido #' . (int)$order->id_pedido . ' - Animalios';

ob_start();
?>

<div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
  <h1 class="page-title" style="margin-bottom:0;">Pedido #<?= (int)$order->id_pedido ?></h1>
  <a class="btn btn--sm" href="<?= htmlspecialchars(route('orders.index'), ENT_QUOTES, 'UTF-8') ?>">← Volver</a>
</div>

<div class="panel" style="margin-top:14px;">
  <div class="panel__body">
    <div style="display:flex; gap:18px; flex-wrap:wrap">
      <div><strong>Fecha:</strong> <?= htmlspecialchars((string)$order->fecha_creacion, ENT_QUOTES, 'UTF-8') ?></div>
      <div><strong>Total:</strong> $ <?= number_format((float)$order->total, 2, ',', '.') ?></div>
    </div>

    <h2 class="section-title" style="text-align:left; margin:18px 0 10px">Productos</h2>
    <div class="tablewrap">
      <table class="ui" aria-label="items">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Precio</th>
            <th>Cantidad</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order->items as $item): ?>
            <tr>
              <td><?= htmlspecialchars((string)($item->product->nombre ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
              <td>$ <?= number_format((float)$item->precio_unitario, 2, ',', '.') ?></td>
              <td><?= (int)$item->cantidad ?></td>
              <td>$ <?= number_format((float)$item->precio_unitario * (int)$item->cantidad, 2, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h2 class="section-title" style="text-align:left; margin:18px 0 10px">Historial</h2>
    <ul style="margin:0; padding-left:18px;">
      <?php foreach ($order->history as $h): ?>
        <li><?= htmlspecialchars((string)$h->fecha_hora, ENT_QUOTES, 'UTF-8') ?> — <strong><?= htmlspecialchars((string)$h->estado, ENT_QUOTES, 'UTF-8') ?></strong></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
