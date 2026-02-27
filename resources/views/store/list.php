<?php
/** @var array $products */
/** @var array $categories */
/** @var array $subcategories */
/** @var array $brands */
/** @var array $filters */

$title = 'Tienda - Animalios';

$pawSvg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 30c-4 0-8-5-8-10s3-9 7-9 7 4 7 9-2 10-6 10Zm24 0c-4 0-8-5-8-10s3-9 7-9 7 4 7 9-2 10-6 10ZM14 40c-4 0-7-4-7-8s2-7 6-7 6 3 6 7-1 8-5 8Zm36 0c-4 0-7-4-7-8s2-7 6-7 6 3 6 7-1 8-5 8Zm-18 2c8 0 18 6 18 14 0 5-4 8-18 8S14 61 14 56c0-8 10-14 18-14Z" fill="currentColor" opacity=".45"/></svg>';

$data = $products['data'] ?? [];
$page = (int)($products['page'] ?? 1);
$per = (int)($products['perPage'] ?? 12);
$total = (int)($products['total'] ?? 0);
$pages = (int)ceil($total / max(1, $per));

// Clean filters for querystring
$qsBase = array_filter([
  'q' => $filters['q'] ?? null,
  'id_categoria' => $filters['id_categoria'] ?? null,
  'id_subcategoria' => $filters['id_subcategoria'] ?? null,
  'id_marca' => $filters['id_marca'] ?? null,
], fn($v) => $v !== null && $v !== '');

ob_start();
?>

<div class="store-layout">
  <aside class="panel filters">
    <div class="filters__title">Filtros:</div>

    <form method="GET" action="<?= htmlspecialchars(route('store.index'), ENT_QUOTES, 'UTF-8') ?>">
      <div class="filters__group">
        <div class="filters__label">Buscar</div>
        <input type="text" name="q" placeholder="Buscar" value="<?= htmlspecialchars((string)($filters['q'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>
      <div class="filters__actions">
        <button class="btn btn--sm btn--primary" type="submit">Filtrar</button>
        <a class="btn btn--sm" href="<?= htmlspecialchars(route('store.index'), ENT_QUOTES, 'UTF-8') ?>">Limpiar</a>
      </div>
    </form>
  </aside>

  <section>
    <div style="display:flex; align-items:baseline; justify-content:space-between; gap:12px; flex-wrap:wrap;">
      <h1 class="page-title" style="margin-bottom:0;">Tienda</h1>
      <div class="muted" style="font-weight:800; font-size:12px;">
        <?= (int)$total ?> productos
      </div>
    </div>

    <?php if (empty($data)): ?>
      <div class="panel" style="margin-top:14px;"><div class="panel__body">
        <p class="muted">No hay productos para mostrar.</p>
      </div></div>
    <?php else: ?>
      <div class="grid grid--products" style="margin-top:14px;">
        <?php foreach ($data as $p): ?>
          <div class="product">
            <a href="<?= htmlspecialchars(route('store.show', ['id' => $p->id_producto]), ENT_QUOTES, 'UTF-8') ?>">
              <div class="product__img"><?= $pawSvg ?></div>
              <div class="product__name"><?= htmlspecialchars((string)$p->nombre, ENT_QUOTES, 'UTF-8') ?></div>
            </a>
            <div class="product__price">$ <?= number_format((float)$p->precio, 2, ',', '.') ?></div>
            <div class="product__actions">
              <form method="POST" action="<?= htmlspecialchars(route('cart.add'), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id_producto" value="<?= (int)$p->id_producto ?>">
                <input type="hidden" name="cantidad" value="1">
                <button class="btn btn--sm" type="submit">Comprar</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($pages > 1): ?>
        <div class="pagination" aria-label="paginación">
          <?php
            $window = 8;
            $start = max(1, $page - 3);
            $end = min($pages, $start + $window - 1);
            $start = max(1, $end - $window + 1);
          ?>

          <?php if ($page > 1): ?>
            <a class="pagebtn" href="<?= htmlspecialchars(route('store.index') . '?' . http_build_query(array_merge($qsBase, ['page' => $page - 1])), ENT_QUOTES, 'UTF-8') ?>">‹</a>
          <?php endif; ?>

          <?php for ($p = $start; $p <= $end; $p++): ?>
            <a class="pagebtn <?= ($p === $page) ? 'pagebtn--active' : '' ?>" href="<?= htmlspecialchars(route('store.index') . '?' . http_build_query(array_merge($qsBase, ['page' => $p])), ENT_QUOTES, 'UTF-8') ?>">
              <?= $p ?>
            </a>
          <?php endfor; ?>

          <?php if ($page < $pages): ?>
            <a class="pagebtn" href="<?= htmlspecialchars(route('store.index') . '?' . http_build_query(array_merge($qsBase, ['page' => $page + 1])), ENT_QUOTES, 'UTF-8') ?>">›</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
