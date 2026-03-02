<?php
/** @var array $orders */
$title = 'Administrar Pedidos - Animalios';

$data = $orders['data'] ?? [];
$page = (int)($orders['page'] ?? 1);
$per = (int)($orders['perPage'] ?? 15);
$total = (int)($orders['total'] ?? 0);
$pages = (int)ceil($total / max(1, $per));

ob_start();
?>

<div class="panel">
  <div class="panel__body">
    <h1 class="page-title" style="margin:0 0 12px;">Administrar Pedidos</h1>

    <div class="tablewrap">
      <table class="ui" aria-label="pedidos admin">
        <thead>
          <tr>
            <th># Pedido</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Total</th>
            <th>Estado</th>
            <th style="text-align:right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $o): ?>
            <tr>
              <td><?= (int)$o->id_pedido ?></td>
              <td><?= htmlspecialchars((string)($o->user->nombre ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars((string)$o->fecha_creacion, ENT_QUOTES, 'UTF-8') ?></td>
              <td>$ <?= number_format((float)$o->total, 2, ',', '.') ?></td>
              <td><?= htmlspecialchars((string)($o->estado_nombre ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <div class="actions">
                  <a class="btn btn--sm" href="<?= htmlspecialchars(route('admin.orders.show', ['id' => $o->id_pedido]), ENT_QUOTES, 'UTF-8') ?>">Ver</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($pages > 1): ?>
      <div class="pagination" style="margin-top:14px;">
        <?php if ($page > 1): ?>
          <a class="pagebtn" href="<?= htmlspecialchars(route('admin.orders.index') . '?' . http_build_query(['page' => $page - 1]), ENT_QUOTES, 'UTF-8') ?>">‹</a>
        <?php endif; ?>
        <?php for ($p = 1; $p <= $pages; $p++): ?>
          <?php if ($p === 1 || $p === $pages || abs($p - $page) <= 2): ?>
            <a class="pagebtn <?= ($p === $page) ? 'pagebtn--active' : '' ?>" href="<?= htmlspecialchars(route('admin.orders.index') . '?' . http_build_query(['page' => $p]), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
          <?php elseif ($p === 2 && $page > 4): ?>
            <span class="muted">…</span>
          <?php elseif ($p === $pages - 1 && $page < $pages - 3): ?>
            <span class="muted">…</span>
          <?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a class="pagebtn" href="<?= htmlspecialchars(route('admin.orders.index') . '?' . http_build_query(['page' => $page + 1]), ENT_QUOTES, 'UTF-8') ?>">›</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
