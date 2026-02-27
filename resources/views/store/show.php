<?php
/** @var object $product */
$title = (string)$product->nombre . ' - Animalios';

$pawSvg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 30c-4 0-8-5-8-10s3-9 7-9 7 4 7 9-2 10-6 10Zm24 0c-4 0-8-5-8-10s3-9 7-9 7 4 7 9-2 10-6 10ZM14 40c-4 0-7-4-7-8s2-7 6-7 6 3 6 7-1 8-5 8Zm36 0c-4 0-7-4-7-8s2-7 6-7 6 3 6 7-1 8-5 8Zm-18 2c8 0 18 6 18 14 0 5-4 8-18 8S14 61 14 56c0-8 10-14 18-14Z" fill="currentColor" opacity=".45"/></svg>';

ob_start();
?>

<div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
  <h1 class="page-title" style="margin-bottom:0;"><?= htmlspecialchars((string)$product->nombre, ENT_QUOTES, 'UTF-8') ?></h1>
  <a class="btn btn--sm" href="<?= htmlspecialchars(route('store.index'), ENT_QUOTES, 'UTF-8') ?>">← Volver</a>
</div>

<div class="panel" style="margin-top:14px;">
  <div class="panel__body">
    <div style="display:grid; grid-template-columns:320px 1fr; gap:18px; align-items:start;">
      <div class="product__img" style="height:260px; background:#efede7; margin:0;">
        <?= $pawSvg ?>
      </div>

      <div>
        <div style="display:grid; gap:6px; margin-bottom:10px;">
          <div class="muted"><strong>Marca:</strong> <?= htmlspecialchars((string)($product->brand->nombre ?? '—'), ENT_QUOTES, 'UTF-8') ?></div>
          <div class="muted"><strong>Categoría:</strong> <?= htmlspecialchars((string)($product->subcategory->category->nombre ?? '—'), ENT_QUOTES, 'UTF-8') ?></div>
          <div class="muted"><strong>Subcategoría:</strong> <?= htmlspecialchars((string)($product->subcategory->nombre ?? '—'), ENT_QUOTES, 'UTF-8') ?></div>
        </div>

        <div style="font-size:22px; font-weight:900; margin:10px 0 14px;">$ <?= number_format((float)$product->precio, 2, ',', '.') ?></div>

        <?php if (!empty($product->descripcion)): ?>
          <p style="margin:0 0 14px; line-height:1.55;"><?= nl2br(htmlspecialchars((string)$product->descripcion, ENT_QUOTES, 'UTF-8')) ?></p>
        <?php endif; ?>

        <form method="POST" action="<?= htmlspecialchars(route('cart.add'), ENT_QUOTES, 'UTF-8') ?>" style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
          <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
          <input type="hidden" name="id_producto" value="<?= (int)$product->id_producto ?>">
          <div style="max-width:140px;">
            <label for="qty">Cantidad</label>
            <input id="qty" type="number" name="cantidad" value="1" min="1" max="99">
          </div>
          <button class="btn btn--primary" type="submit">Agregar al carrito</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
