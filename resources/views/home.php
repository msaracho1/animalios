<?php
/** @var array $featured */
/** @var array $brands */
$title = 'Animalios';

$pawSvg = '<svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 30c-4 0-8-5-8-10s3-9 7-9 7 4 7 9-2 10-6 10Zm24 0c-4 0-8-5-8-10s3-9 7-9 7 4 7 9-2 10-6 10ZM14 40c-4 0-7-4-7-8s2-7 6-7 6 3 6 7-1 8-5 8Zm36 0c-4 0-7-4-7-8s2-7 6-7 6 3 6 7-1 8-5 8Zm-18 2c8 0 18 6 18 14 0 5-4 8-18 8S14 61 14 56c0-8 10-14 18-14Z" fill="currentColor" opacity=".45"/></svg>';

ob_start();
?>

<section class="hero" aria-label="Promociones">
  <div class="hero__inner">
    <div>
      <h1 class="hero__title">
        <small>NATURAL</small>
        BLACK SALE
      </h1>
      <p class="hero__subtitle">Descuentos en alimento y piedras sanitarias. Encontrá tu marca y aprovechá promos por tiempo limitado.</p>

      <div class="hero__promo" aria-label="badges">
        <span class="pill">HASTA 45% OFF</span>
        <span class="pill">3×2</span>
        <span class="pill">KILOS GRATIS</span>
      </div>

      <div style="margin-top:16px;">
        <a class="btn btn--primary" href="/animalios/public/tienda">COMPRAR</a>
      </div>
    </div>

    <div class="hero__mock" aria-hidden="true">
      <span>Promo visual / carrusel<br> (placeholder)</span>
    </div>
  </div>
</section>

<h2 class="section-title">Productos más vendidos</h2>

<?php if (empty($featured)): ?>
  <p class="muted" style="text-align:center">No hay productos para mostrar.</p>
<?php else: ?>
  <div class="grid grid--products">
    <?php foreach ($featured as $p): ?>
      <div class="product">
        <a href="<?= htmlspecialchars(route('store.show', ['id' => $p->id_producto]), ENT_QUOTES, 'UTF-8') ?>">
          <div class="product__img">
            <?php if (!empty($p->imagen_url)): ?>
              <img src="<?= htmlspecialchars(base_path() . (string)$p->imagen_url, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string)$p->nombre, ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
            <?php else: ?>
              <?= $pawSvg ?>
            <?php endif; ?>
          </div>
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
<?php endif; ?>

<h2 class="section-title">Trabajamos con las mejores marcas</h2>
<div class="brands">
  <?php foreach (($brands ?? []) as $b): ?>
    <div class="brandchip"><?= htmlspecialchars((string)$b->nombre_marca, ENT_QUOTES, 'UTF-8') ?></div>
  <?php endforeach; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/app.php';
