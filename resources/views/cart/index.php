<?php
/** @var array $cart */
/** @var float $total */
$title = 'Carrito - Animalios';

ob_start();
?>

<h1 class="page-title">Carrito</h1>

<?php if (empty($cart)): ?>
  <div class="panel">
    <div class="panel__body">
      <p class="muted">Tu carrito está vacío.</p>
      <a class="btn btn--primary" href="<?= htmlspecialchars(route('store.index'), ENT_QUOTES, 'UTF-8') ?>">Ir a la tienda</a>
    </div>
  </div>
<?php else: ?>

  <form method="POST" action="<?= htmlspecialchars(route('cart.update'), ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">

    <div class="panel">
      <div class="panel__body">
        <div class="tablewrap">
          <table class="ui" aria-label="carrito">
            <thead>
              <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th style="text-align:right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php $i = 0; foreach ($cart as $item): ?>
                <?php
                  $id = (int)$item['id_producto'];
                  $qty = (int)$item['qty'];
                  $price = (float)$item['price'];
                ?>
                <tr>
                  <td><?= htmlspecialchars((string)$item['name'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td>$ <?= number_format($price, 2, ',', '.') ?></td>
                  <td style="max-width:140px">
                    <input type="hidden" name="items[<?= $i ?>][id_producto]" value="<?= $id ?>">
                    <input type="number" min="0" max="99" name="items[<?= $i ?>][qty]" value="<?= $qty ?>">
                  </td>
                  <td>$ <?= number_format($price * $qty, 2, ',', '.') ?></td>
                  <td>
                    <div class="actions">
                      <button class="btn btn--sm btn--danger" type="submit"
                              formaction="<?= htmlspecialchars(route('cart.remove'), ENT_QUOTES, 'UTF-8') ?>"
                              name="id_producto" value="<?= $id ?>"
                              onclick="return confirm('¿Quitar producto del carrito?')">
                        Quitar
                      </button>
                    </div>
                  </td>
                </tr>
              <?php $i++; endforeach; ?>
            </tbody>
          </table>
        </div>

        <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-top:14px;">
          <div style="font-weight:900;">Total: $ <?= number_format((float)$total, 2, ',', '.') ?></div>
          <div class="actions">
            <button class="btn btn--sm" type="submit">Actualizar carrito</button>
            <button class="btn btn--sm btn--primary" type="submit" formaction="<?= htmlspecialchars(route('checkout'), ENT_QUOTES, 'UTF-8') ?>">Finalizar compra</button>
          </div>
        </div>
      </div>
    </div>
  </form>

<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
