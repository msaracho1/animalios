<?php
declare(strict_types=1);

use App\Core\Router;
use App\Core\Session;
use App\Core\Auth;

function route(string $name, array $params = []): string {
    $url = \App\Core\Router::urlFor($name, $params);
    return base_path() . $url; // <-- clave
}

function session(string $key, $default = null) {
    return Session::getFlash($key) ?? Session::get($key, $default);
}

function auth() {
    return Auth::instance();
}

function old(string $key, $default = '') {
    $old = \App\Core\Session::get('_old', []);
    return $old[$key] ?? $default;
}

function request(string $key, $default = null) {
    return $_GET[$key] ?? $default;
}

function base_path(): string {
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    return ($base === '' || $base === '/') ? '' : $base;
}

