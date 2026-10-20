<?php
declare(strict_types=1);

function loadJson(string $path): array
{
    if (!is_readable($path)) {
        throw new RuntimeException('Файл данных недоступен.');
    }
    $raw = file_get_contents($path);
    if ($raw === false) {
        throw new RuntimeException('Не удалось прочитать файл данных.');
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        throw new RuntimeException('Файл данных повреждён.');
    }
    return $data;
}