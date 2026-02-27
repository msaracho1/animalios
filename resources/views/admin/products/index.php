<?php
/** @var array $products */
$title = 'Administrar Productos - Animalios';

$data = $products['data'] ?? [];
$page = (int)($products['page'] ?? 1);
$per = (int)($products['perPage'] ?? 15);
$total = (int)($products['total'] ?? 0);
$pages = (int)ceil($total / max(1, $per));

ob_start();
?>

<div class="panel">
  <div class="panel__body">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
      <h1 class="page-title" style="margin:0;">Administrar Productos</h1>
      <a class="btn btn--sm btn--primary" href="<?= htmlspecialchars(route('admin.products.create'), ENT_QUOTES, 'UTF-8') ?>">Agregar producto</a>
    </div>

    <div class="tablewrap" style="margin-top:12px;">
      <table class="ui" aria-label="productos">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Marca</th>
            <th>Subcategoría</th>
            <th>Precio</th>
            <th>Stock</th>
            <th style="text-align:right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $p): ?>
            <tr>
              <td><?= (int)$p->id_producto ?></td>
              <td><?= htmlspecialchars((string)$p->nombre, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars((string)($p->brand->nombre ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars((string)($p->subcategory->nombre ?? '—'), ENT_QUOTES, 'UTF-8') ?></td>
              <td>$ <?= number_format((float)$p->precio, 2, ',', '.') ?></td>
              <td><?= (int)$p->stock ?></td>
              <td>
                <div class="actions">
                  <a class="btn btn--sm btn--ok" href="<?= htmlspecialchars(route('admin.products.edit', ['id' => $p->id_producto]), ENT_QUOTES, 'UTF-8') ?>">Editar</a>
                  <form method="POST" action="<?= htmlspecialchars(route('admin.products.destroy', ['id' => $p->id_producto]), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                    <button class="btn btn--sm btn--danger" type="submit" onclick="return confirm('¿Eliminar producto?')">Baja</button>
                  </form>
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
          <a class="pagebtn" href="<?= htmlspecialchars(route('admin.products.index') . '?' . http_build_query(['page' => $page - 1]), ENT_QUOTES, 'UTF-8') ?>">‹</a>
        <?php endif; ?>
        <?php for ($p = 1; $p <= $pages; $p++): ?>
          <?php if ($p === 1 || $p === $pages || abs($p - $page) <= 2): ?>
            <a class="pagebtn <?= ($p === $page) ? 'pagebtn--active' : '' ?>" href="<?= htmlspecialchars(route('admin.products.index') . '?' . http_build_query(['page' => $p]), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
          <?php elseif ($p === 2 && $page > 4): ?>
            <span class="muted">…</span>
          <?php elseif ($p === $pages - 1 && $page < $pages - 3): ?>
            <span class="muted">…</span>
          <?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $pages): ?>
          <a class="pagebtn" href="<?= htmlspecialchars(route('admin.products.index') . '?' . http_build_query(['page' => $page + 1]), ENT_QUOTES, 'UTF-8') ?>">›</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
