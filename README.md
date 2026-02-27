# Animalios – Migración de Laravel a PHP Vanilla

Este proyecto es una versión **PHP vanilla (sin framework)** basada en tu proyecto Laravel `animalios`.

## 1) Requisitos
- PHP 8.1+ (ideal 8.2+)
- MySQL/MariaDB

## 2) Configurar la base de datos
En tu Laravel original no encontré migraciones con las tablas del dominio (producto/pedido/etc). El código asume estas tablas (por los modelos y consultas):

- `rol(id_rol, nombre)`
- `usuario(id_usuario, nombre, email, contraseña, id_rol)`
- `marca(id_marca, nombre)`
- `categoria(id_categoria, nombre)`
- `subcategoria(id_subcategoria, nombre, id_categoria)`
- `producto(id_producto, nombre, descripcion, precio, stock, id_marca, id_subcategoria)`
- `pedido(id_pedido, id_usuario, fecha, total)`
- `detalle_pedido(id_detalle, id_pedido, id_producto, cantidad, precio)`
- `historial_pedido(id_historial, id_pedido, estado, fecha)`

### SQL sugerido (mínimo viable)
> Ajustalo a tu DB real si ya la tenías.

```sql
CREATE TABLE rol (
  id_rol INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE usuario (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  contraseña VARCHAR(45) NOT NULL,
  id_rol INT NOT NULL,
  FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
);

CREATE TABLE marca (
  id_marca INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL
);

CREATE TABLE categoria (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL
);

CREATE TABLE subcategoria (
  id_subcategoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  id_categoria INT NOT NULL,
  FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
);

CREATE TABLE producto (
  id_producto INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT NULL,
  precio DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  id_marca INT NOT NULL,
  id_subcategoria INT NOT NULL,
  FOREIGN KEY (id_marca) REFERENCES marca(id_marca),
  FOREIGN KEY (id_subcategoria) REFERENCES subcategoria(id_subcategoria)
);

CREATE TABLE pedido (
  id_pedido INT AUTO_INCREMENT PRIMARY KEY,
  id_usuario INT NOT NULL,
  fecha DATETIME NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
);

CREATE TABLE detalle_pedido (
  id_detalle INT AUTO_INCREMENT PRIMARY KEY,
  id_pedido INT NOT NULL,
  id_producto INT NOT NULL,
  cantidad INT NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido),
  FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

CREATE TABLE historial_pedido (
  id_historial INT AUTO_INCREMENT PRIMARY KEY,
  id_pedido INT NOT NULL,
  estado VARCHAR(50) NOT NULL,
  fecha DATETIME NOT NULL,
  FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido)
);

INSERT INTO rol(nombre) VALUES ('admin'),('cliente');
```

## 3) Configurar `.env`
Editá `.env` y asegurate de tener:
- `DB_CONNECTION=mysql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

## 4) Levantar el proyecto
Desde la carpeta del proyecto:

```bash
php -S localhost:8000 -t public
```

Abrí `http://localhost:8000`.

---

# 5) Paso a paso: cómo “recrear” la migración

La idea es mapear **conceptos Laravel → piezas vanilla**.

## Paso A — Front controller
En Laravel entra por `public/index.php` (y el kernel). En vanilla hacemos lo mismo:
- `public/index.php` carga autoload, env, sesión, router y despacha.

## Paso B — Router
En Laravel tenías rutas en `routes/web.php`. En vanilla:
- `src/Core/Router.php` registra rutas `GET/POST`.
- Se agregan nombres (`->name('...')`) para poder usar `route('name')` en las vistas.

## Paso C — Sesión, CSRF y Auth
Laravel te daba:
- `session()`, flash, middleware `auth`, `@csrf`, etc.

Vanilla:
- `src/Core/Session.php` maneja sesión + flash + token CSRF.
- El router valida CSRF para `POST`.
- `src/Core/Auth.php` guarda `user_id` en sesión y trae el usuario desde DB.

## Paso D — “Modelos” (Eloquent) → Repositories
En Laravel usabas Eloquent (`Product::with(...)->paginate(...)`).

En vanilla reemplazamos por repositorios con SQL:
- `src/Repositories/*Repository.php`
- Se usan `JOIN` para traer `brand/subcategory/category`, etc.

## Paso E — Controllers
Copiamos la intención de tus controllers Laravel, pero:
- validación: `Request->validate([...])`
- DB: `DB::begin/commit/rollBack` en checkout
- consultas: repositorios

## Paso F — Views (Blade)
Para no reescribir todo de cero, hay un mini-compiler “blade-lite”:
- `src/Core/View.php` compila `*.blade.php` a `storage/cache`.
- Soporta lo que usa tu proyecto: `@extends/@section/@yield`, `@if/@foreach`, `@auth`, `@csrf`, `{{ }}`, `@selected`.

---

# 6) Diferencias importantes vs Laravel
- No hay `artisan`, ni service container, ni Eloquent.
- La validación es mínima (suficiente para este proyecto).
- Los controllers admin en tu repo original estaban “duplicados/vacíos” en `app/Http/Controllers/Admin/*` y los funcionales en `app/Http/Controllers/*.php` con namespace Admin. En vanilla dejé una sola versión en `src/Controllers/Admin/*`.

---

Si querés, el próximo paso es que me confirmes **tu esquema real de DB** (o me pases un dump `.sql`) y ajustamos queries/joins a tu estructura exacta.
