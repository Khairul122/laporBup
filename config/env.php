<?php
function loadEnv(string $basePath): void
{
    // Tentukan APP_ENV dari system environment atau default ke 'local'
    $appEnv = getenv('APP_ENV') ?: 'local';

    $files = [
        $basePath . '/.env',                    // 3 - fallback
        $basePath . '/.env.' . $appEnv,         // 2 - environment spesifik
        $basePath . '/.env.local',              // 1 - override peribadi
    ];

    foreach ($files as $file) {
        _parseEnvFile($file);
    }
}
function _parseEnvFile(string $path): void
{
    if (!is_file($path) || !is_readable($path)) {
        return;
    }

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);

        // Skip komen dan baris kosong
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        $value = trim($value, "\"'");

        // Jangan timpa nilai yang sudah wujud dalam environment
        if (getenv($key) === false && !isset($_ENV[$key])) {
            putenv("$key=$value");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    }
}
function env(string $key, $default = null)
{
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    // Cast boolean strings
    return match (strtolower($value)) {
        'true',  '(true)'  => true,
        'false', '(false)' => false,
        'null',  '(null)'  => null,
        'empty', '(empty)' => '',
        default            => $value,
    };
}

function isEnv($environments): bool
{
    $current = env('APP_ENV', 'local');

    return in_array($current, (array) $environments, true);
}
function isDebug(): bool
{
    return (bool) env('APP_DEBUG', !isEnv('production'));
}
