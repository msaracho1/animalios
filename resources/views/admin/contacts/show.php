<?php
/** @var object $contact */
/** @var array $states */
$title = 'Consulta ' . htmlspecialchars((string)$contact->numero_ticket, ENT_QUOTES, 'UTF-8');

ob_start();
?>

<div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
  <h1 class="page-title" style="margin-bottom:0;">Consulta <?= htmlspecialchars((string)$contact->numero_ticket, ENT_QUOTES, 'UTF-8') ?></h1>
  <a class="btn btn--sm" href="<?= htmlspecialchars(route('admin.contacts.index'), ENT_QUOTES, 'UTF-8') ?>">← Volver</a>
</div>

<div class="panel" style="margin-top:14px;">
  <div class="panel__body">
    <div style="display:grid; grid-template-columns:repeat(2, minmax(220px,1fr)); gap:8px 16px;">
      <div><strong>Nombre:</strong> <?= htmlspecialchars((string)$contact->nombre, ENT_QUOTES, 'UTF-8') ?></div>
      <div><strong>Email:</strong> <?= htmlspecialchars((string)$contact->email, ENT_QUOTES, 'UTF-8') ?></div>
      <div><strong>Asunto:</strong> <?= htmlspecialchars((string)$contact->asunto, ENT_QUOTES, 'UTF-8') ?></div>
      <div><strong>Prioridad:</strong> <?= htmlspecialchars((string)$contact->prioridad, ENT_QUOTES, 'UTF-8') ?></div>
      <div><strong>Estado:</strong> <?= htmlspecialchars((string)$contact->nombre_estado, ENT_QUOTES, 'UTF-8') ?></div>
      <div><strong>Fecha:</strong> <?= htmlspecialchars((string)$contact->fecha_creacion, ENT_QUOTES, 'UTF-8') ?></div>
    </div>

    <h2 class="section-title" style="text-align:left; margin:16px 0 10px;">Mensaje</h2>
    <div class="card"><div class="card__body"><?= nl2br(htmlspecialchars((string)$contact->mensaje, ENT_QUOTES, 'UTF-8')) ?></div></div>

    <form method="POST" action="<?= htmlspecialchars(route('admin.contacts.status', ['id' => $contact->id_contacto]), ENT_QUOTES, 'UTF-8') ?>" style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
      <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
      <select name="id_estado_contacto" required style="max-width:220px;">
        <?php foreach ($states as $state): ?>
          <option value="<?= (int)$state->id_estado_contacto ?>" <?= (int)$state->id_estado_contacto === (int)$contact->id_estado_contacto ? 'selected' : '' ?>>
            <?= htmlspecialchars((string)$state->nombre_estado, ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn--sm btn--primary" type="submit">Cambiar estado</button>
    </form>
  </div>
</div>

<div class="panel" style="margin-top:14px;">
  <div class="panel__body">
    <h2 class="section-title" style="text-align:left; margin-top:0;">Responder consulta</h2>

    <form method="POST" action="<?= htmlspecialchars(route('admin.contacts.respond', ['id' => $contact->id_contacto]), ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">
      <div class="form__row">
        <label for="respuesta">Respuesta</label>
        <textarea id="respuesta" name="respuesta" required><?= htmlspecialchars((string)old('respuesta'), ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>
      <button class="btn btn--primary" type="submit">Guardar respuesta</button>
    </form>

    <h3 style="margin:18px 0 10px; font-size:16px; font-weight:800;">Historial de respuestas</h3>
    <?php if (empty($contact->responses)): ?>
      <p class="muted">Todavía no hay respuestas registradas.</p>
    <?php else: ?>
      <ul style="margin:0; padding-left:18px;">
        <?php foreach ($contact->responses as $r): ?>
          <li style="margin-bottom:8px;">
            <strong><?= htmlspecialchars((string)($r->nombre . ' ' . $r->apellido), ENT_QUOTES, 'UTF-8') ?></strong>
            (<?= htmlspecialchars((string)$r->fecha_respuesta, ENT_QUOTES, 'UTF-8') ?>):
            <?= htmlspecialchars((string)$r->respuesta, ENT_QUOTES, 'UTF-8') ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
