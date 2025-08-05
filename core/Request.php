<?php

namespace PHPFramework;

class Request
{

    public string $uri;
    public string $rawUri;

    public array $post;
    public array $get;
    public array $files;

    public array $json;
    public string $input;

    public function __construct($uri)
    {
        $this->rawUri = $uri;
        $this->uri = trim(urldecode($uri), '/');
        $this->post = $_POST;
        $this->get = $_GET;
        $this->files = $_FILES;
        $this->json = [];
        $this->input = file_get_contents('php://input');

        $this->parseJsonInput();
    }

    // Парсинг JSON
    protected function parseJsonInput(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') !== false) {
            $jsonData = json_decode($this->input, true);
            foreach($jsonData as $key => $val) {
                $key = $key;
                $jsonData[$key] = $val;
            }
            
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->json = $jsonData;
            }
        }
    }

    // Получаем JSON данные
    public function getJson(mixed $keys = null, mixed $default = null): mixed
    {
        if ($keys === null) {
            return $this->json;
        }

        if (is_array($keys)) {
            $result = [];
            foreach ($keys as $key) {
                $value = $this->json;
                foreach (explode('.', $key) as $segment) {
                    if (is_array($value) && array_key_exists($segment, $value)) {
                        $value = $value[$segment];
                    } else {
                        $value = $default;
                        break;
                    }
                }
                $result[$key] = $value;
            }
            return $result ?? $default;
        }

        return $this->json[$keys] ?? $default;
    }

    // Проверяем, является ли запрос JSON
    public function isJson(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return strpos($contentType, 'application/json') !== false;
    }

    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function isGet(): bool
    {
        return $this->getMethod() == 'GET';
    }

    public function isPost(): bool
    {
        return $this->getMethod() == 'POST';
    }

    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    public function get($name, $default = null): ?string
    {
        return $this->get[$name] ?? $default;
    }

    public function post($name, $default = null): ?string
    {
        return $this->post[$name] ?? $default;
    }

    public function getPath(): string
    {
        return $this->removeQueryString();
    }

    protected function removeQueryString(): string
    {
        if ($this->uri) {
            $params = explode("?", $this->uri);
            return trim($params[0], '/');
        }
        return "";
    }

    public function getData($fileKey = '', $fileValue = ''): array
    {
        $data = [];
        $request_data = $this->isPost() ? $_POST : $_GET;
        if ($fileKey && $fileValue) {
            if (is_array($fileKey) && is_array($fileValue)) {
                // Обработка массива ключей и значений
                foreach ($fileKey as $index => $key) {
                    if (array_key_exists($index, $fileValue)) {
                        $request_data[$key] = $fileValue[$index];
                    }
                }
            } else if (!is_array($fileKey) && !is_array($fileValue)) {
                // Обратная совместимость с одиночными значениями
                $request_data[$fileKey] = $fileValue;
            }
        }
        foreach ($request_data as $k => $v) {
            if (is_string($v)) {
                $v = trim($v);
            }
            $data[$k] = $v;
        }
        return $data;
    }

    public function getDataVal(array $keys): array
    {
        $requestData = $this->getData();
        $result = [];
        
        foreach ($keys as $key) {
            $result[valid_data($key)] = valid_data($requestData[$key]) ?? null;
        }
        
        return $result;
    }
}