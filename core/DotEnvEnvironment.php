<?php

namespace PHPFramework;

class DotEnvEnvironment
{
    // загружает в $_ENV константы из файла .env
    public function load(string $path): void
    {
        $filePath = $path . '/.env';
        
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Пропускаем комментарии и невалидные строки
            if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            $value = $this->removeQuotes($value);

            // Устанавливаем переменные
            if ($key !== '') {
                putenv("$key=$value");
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
            }
        }
    }

    // Удаляем окружающие кавычки
    private function removeQuotes(string $value): string
    {
        $value = trim($value);
        $firstChar = $value[0] ?? '';
        $lastChar = substr($value, -1);
        $quoteChars = ['"', "'"];

        if (in_array($firstChar, $quoteChars, true) && $firstChar === $lastChar) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}