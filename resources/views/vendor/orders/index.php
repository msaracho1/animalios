<?php
/** @var array $orders */
/** @var array $statuses */
$title = 'Pedidos de clientes - Vendedor';

$data = $orders['data'] ?? [];
$page = (int)($orders['page'] ?? 1);
$per = (int)($orders['perPage'] ?? 15);
$total = (int)($orders['total'] ?? 0);
$pages = (int)ceil($total / max(1, $per));

ob_start();
?>

<div class="panel">
  <div class="panel__body">
    <h1 class="page-title" style="margin:0 0 12px;">Gestión de pedidos</h1>

    <div class="tablewrap">
      <table class="ui" aria-label="pedidos vendedor">
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
                <form method="POST" action="<?= htmlspecialchars(route('vendor.orders.status', ['id' => $o->id_pedido]), ENT_QUOTES, 'UTF-8') ?>" class="actions">
                  <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                  <select name="id_estado_pedido" required style="max-width:180px;">
                    <?php foreach ($statuses as $status): ?>
                      <option value="<?= (int)$status->id_estado_pedido ?>" <?= (int)$status->id_estado_pedido === (int)$o->id_estado_pedido ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string)$status->nombre_estado, ENT_QUOTES, 'UTF-8') ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button class="btn btn--sm btn--primary" type="submit">Actualizar</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($pages > 1): ?>
      <div class="pagination" style="margin-top:14px;">
        <?php if ($page > 1): ?>
          <a class="pagebtn" href="<?= htmlspecialchars(route('vendor.orders.index') . '?' . http_build_query(['page' => $page - 1]), ENT_QUOTES, 'UTF-8') ?>">‹</a>
        <?php endif; ?>
        <?php for ($p = 1; $p <= $pages; $p++): ?>
          <a class="pagebtn <?= ($p === $page) ? 'pagebtn--active' : '' ?>" href="<?= htmlspecialchars(route('vendor.orders.index') . '?' . http_build_query(['page' => $p]), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a class="pagebtn" href="<?= htmlspecialchars(route('vendor.orders.index') . '?' . http_build_query(['page' => $page + 1]), ENT_QUOTES, 'UTF-8') ?>">›</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
