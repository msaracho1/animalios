<?php
/** @var array $contacts */
$title = 'Consultas - Admin';

$data = $contacts['data'] ?? [];
$page = (int)($contacts['page'] ?? 1);
$per = (int)($contacts['perPage'] ?? 15);
$total = (int)($contacts['total'] ?? 0);
$pages = (int)ceil($total / max(1, $per));

ob_start();
?>

<div class="panel">
  <div class="panel__body">
    <h1 class="page-title" style="margin:0 0 12px;">Consultas de contacto</h1>

    <div class="tablewrap">
      <table class="ui" aria-label="consultas">
        <thead>
          <tr>
            <th>Ticket</th>
            <th>Nombre</th>
            <th>Asunto</th>
            <th>Prioridad</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th style="text-align:right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($data as $c): ?>
            <tr>
              <td><?= htmlspecialchars((string)$c->numero_ticket, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars((string)$c->nombre, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars((string)$c->asunto, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars((string)$c->prioridad, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars((string)$c->nombre_estado, ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars((string)$c->fecha_creacion, ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <div class="actions">
                  <a class="btn btn--sm" href="<?= htmlspecialchars(route('admin.contacts.show', ['id' => $c->id_contacto]), ENT_QUOTES, 'UTF-8') ?>">Ver</a>
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
          <a class="pagebtn" href="<?= htmlspecialchars(route('admin.contacts.index') . '?' . http_build_query(['page' => $page - 1]), ENT_QUOTES, 'UTF-8') ?>">‹</a>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $pages; $p++): ?>
          <a class="pagebtn <?= ($p === $page) ? 'pagebtn--active' : '' ?>" href="<?= htmlspecialchars(route('admin.contacts.index') . '?' . http_build_query(['page' => $p]), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
        <?php endfor; ?>

        <?php if ($page < $pages): ?>
          <a class="pagebtn" href="<?= htmlspecialchars(route('admin.contacts.index') . '?' . http_build_query(['page' => $page + 1]), ENT_QUOTES, 'UTF-8') ?>">›</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../../layouts/app.php';
