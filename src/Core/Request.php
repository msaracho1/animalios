<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly array $query,
        public readonly array $post,
    ) {}

public static function fromGlobals(): self
{
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

    $uri  = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';

    // Base path donde vive el index.php (ej: /animalios/public)
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    if ($base === '/') $base = '';

    // Si la URL empieza con el base, lo recortamos para que el router vea "/"
    if ($base !== '' && str_starts_with($path, $base)) {
        $path = substr($path, strlen($base));
        if ($path === '') $path = '/';
    }

    return new self(
        $method,
        $path,
        $_GET ?? [],
        $_POST ?? []
    );
}

    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function filled(string $key): bool
    {
        $v = $this->input($key);
        return !($v === null || $v === '' || (is_array($v) && empty($v)));
    }

    /**
     * Very small validator for this project.
     * Supported rules: required, integer, numeric, email, string, min:n, max:n, array, nullable
     */
    public function validate(array $rules): array
    {
        $data = [];
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $fieldRules = is_array($fieldRules) ? $fieldRules : explode('|', (string)$fieldRules);
            $value = $this->input($field);
            $isNullable = in_array('nullable', $fieldRules, true);

            if ($value === null || $value === '') {
                if (in_array('required', $fieldRules, true) && !$isNullable) {
                    $errors[$field] = 'Campo requerido.';
                    continue;
                }
                $data[$field] = $value;
                continue;
            }

            foreach ($fieldRules as $rule) {
                if ($rule === 'required' || $rule === 'nullable') continue;

                if ($rule === 'string' && !is_string($value)) $errors[$field] = 'Debe ser texto.';
                if ($rule === 'integer' && filter_var($value, FILTER_VALIDATE_INT) === false) $errors[$field] = 'Debe ser entero.';
                if ($rule === 'numeric' && !is_numeric($value)) $errors[$field] = 'Debe ser numérico.';
                if ($rule === 'email' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) $errors[$field] = 'Email inválido.';
                if ($rule === 'array' && !is_array($value)) $errors[$field] = 'Debe ser un array.';

                if (str_starts_with($rule, 'min:')) {
                    $n = (int)substr($rule, 4);
                    if (is_string($value)) {
                        if (mb_strlen($value) < $n) $errors[$field] = "Mínimo $n caracteres";
                    } elseif (is_numeric($value) && (float)$value < $n) {
                        $errors[$field] = "Mínimo $n";
                    }
                }
                if (str_starts_with($rule, 'max:')) {
                    $n = (int)substr($rule, 4);
                    if (is_string($value)) {
                        if (mb_strlen($value) > $n) $errors[$field] = "Máximo $n caracteres";
                    } elseif (is_numeric($value) && (float)$value > $n) {
                        $errors[$field] = "Máximo $n";
                    }
                }
            }

            $data[$field] = $value;
        }

        if (!empty($errors)) {
            Session::put('_old', $this->post);
            Session::flash('error', 'Validación fallida: ' . implode(' ', array_values($errors)));
            Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
            exit;
        }

        return $data;
    }
}
