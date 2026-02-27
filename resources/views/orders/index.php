<?php
/** @var array $orders */
$title = 'Mis pedidos - Animalios';

ob_start();
?>

<h1 class="page-title">Historial de Pedidos</h1>

<?php if (empty($orders)): ?>
  <div class="panel"><div class="panel__body">
    <p class="muted">No tenés pedidos todavía.</p>
    <a class="btn btn--primary" href="<?= htmlspecialchars(route('store.index'), ENT_QUOTES, 'UTF-8') ?>">Ir a la tienda</a>
  </div></div>
<?php else: ?>
  <div class="panel">
    <div class="panel__body">
      <div class="tablewrap">
        <table class="ui" aria-label="pedidos">
          <thead>
            <tr>
              <th># Pedido</th>
              <th>Fecha</th>
              <th>Total</th>
              <th>Estado</th>
              <th style="text-align:right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
              <?php $last = $o->history[0] ?? null; ?>
              <tr>
                <td><?= (int)$o->id_pedido ?></td>
                <td><?= htmlspecialchars((string)$o->fecha_creacion, ENT_QUOTES, 'UTF-8') ?></td>
                <td>$ <?= number_format((float)$o->total, 2, ',', '.') ?></td>
                <td><?= htmlspecialchars((string)($last->estado ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                  <div class="actions">
                    <a class="btn btn--sm" href="<?= htmlspecialchars(route('orders.show', ['id' => $o->id_pedido]), ENT_QUOTES, 'UTF-8') ?>">Ver detalles</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
