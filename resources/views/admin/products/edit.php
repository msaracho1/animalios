<?php
/** @var object $product */
/** @var array $brands */
/** @var array $subcategories */
$title = 'Editar producto - Animalios';

ob_start();
?>

<div class="panel">
  <div class="panel__body">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
      <h1 class="page-title" style="margin:0;">Editar producto #<?= (int)$product->id_producto ?></h1>
      <a class="btn btn--sm" href="<?= htmlspecialchars(route('admin.products.index'), ENT_QUOTES, 'UTF-8') ?>">← Volver</a>
    </div>

    <form method="POST" action="<?= htmlspecialchars(route('admin.products.update', ['id' => $product->id_producto]), ENT_QUOTES, 'UTF-8') ?>" class="form" style="margin:14px 0 0; max-width:780px;">
      <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">

      <div class="form__row">
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="<?= htmlspecialchars((string)old('nombre', $product->nombre), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="form__row">
        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion"><?= htmlspecialchars((string)old('descripcion', $product->descripcion ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form__row">
          <label for="precio">Precio</label>
          <input id="precio" type="number" step="0.01" name="precio" value="<?= htmlspecialchars((string)old('precio', (string)$product->precio), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
        <div class="form__row">
          <label for="stock">Stock</label>
          <input id="stock" type="number" name="stock" value="<?= htmlspecialchars((string)old('stock', (string)$product->stock), ENT_QUOTES, 'UTF-8') ?>" required>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form__row">
          <label for="marca">Marca</label>
          <select id="marca" name="id_marca" required>
            <?php foreach ($brands as $b): ?>
              <option value="<?= (int)$b->id_marca ?>" <?= ((int)$b->id_marca === (int)$product->id_marca) ? 'selected' : '' ?>>
                <?= htmlspecialchars((string)$b->nombre_marca, ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form__row">
          <label for="sub">Subcategoría</label>
          <select id="sub" name="id_subcategoria" required>
            <?php foreach ($subcategories as $s): ?>
              <option value="<?= (int)$s->id_subcategoria ?>" <?= ((int)$s->id_subcategoria === (int)$product->id_subcategoria) ? 'selected' : '' ?>>
                <?= htmlspecialchars((string)$s->nombre_subcategoria, ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form__actions" style="justify-content:flex-end;">
        <button class="btn btn--primary" type="submit">Actualizar</button>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
