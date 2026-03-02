<?php
$title = 'Nosotros - Animalios';

ob_start();
?>

<div class="panel">
  <div class="panel__body about-grid">
    <div>
      <h1 class="page-title">¿Quiénes somos?</h1>
      <h2 style="margin:0 0 10px; font-size:24px; font-weight:800;">Animalíos</h2>
      <p class="muted" style="line-height:1.7; margin:0;">
        En Animalíos acompañamos a cada familia en el cuidado diario de sus mascotas con productos seleccionados,
        atención cercana y una experiencia simple para comprar online. Nuestro equipo combina amor por los animales,
        asesoramiento responsable y envíos rápidos para que encuentres todo lo que tu perro o gato necesita en un solo lugar.
      </p>
    </div>
    <div>
      <img src="<?= htmlspecialchars(base_path() . '/img/BANNER.png', ENT_QUOTES, 'UTF-8') ?>" alt="Animalíos" class="about-image" loading="lazy">
    </div>
  </div>
</div>

<div class="panel" style="margin-top:16px;">
  <div class="panel__body">
    <h2 class="section-title" style="text-align:left; margin-top:0;">Formulario de contacto</h2>
    <form method="POST" action="<?= htmlspecialchars(route('contact.store'), ENT_QUOTES, 'UTF-8') ?>" class="form" style="max-width:100%;">
      <input type="hidden" name="_token" value="<?= htmlspecialchars(\App\Core\Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>">

      <div class="form__row">
        <label for="nombre">Nombre</label>
        <input id="nombre" type="text" name="nombre" value="<?= htmlspecialchars((string)old('nombre'), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="form__row">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="<?= htmlspecialchars((string)old('email'), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="form__row">
        <label for="asunto">Asunto</label>
        <input id="asunto" type="text" name="asunto" value="<?= htmlspecialchars((string)old('asunto'), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="form__row">
        <label for="prioridad">Prioridad</label>
        <select id="prioridad" name="prioridad" required>
          <option value="baja">Baja</option>
          <option value="media" selected>Media</option>
          <option value="alta">Alta</option>
        </select>
      </div>

      <div class="form__row">
        <label for="mensaje">Mensaje</label>
        <textarea id="mensaje" name="mensaje" required><?= htmlspecialchars((string)old('mensaje'), ENT_QUOTES, 'UTF-8') ?></textarea>
      </div>

      <div class="form__actions" style="justify-content:flex-start;">
        <button class="btn btn--primary" type="submit">Enviar consulta</button>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layouts/app.php';
