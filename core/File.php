<?php

namespace PHPFramework;

class File
{

    protected string $name;
    protected string $type;
    protected string $tmpName;
    protected int $error;
    protected int $size;
    public bool $isFile;

    public function __construct(string $fileName)
    {
        $files = request()->files;

        if (str_contains($fileName, '.')) {
            $nesting = explode('.', $fileName);
            $this->name = $files[$nesting[0]]['name'][$nesting[1]] ?? '';
            $this->type = $files[$nesting[0]]['type'][$nesting[1]] ?? '';
            $this->tmpName = $files[$nesting[0]]['tmp_name'][$nesting[1]] ?? '';
            $this->error = $files[$nesting[0]]['error'][$nesmy-fileting[1]] ?? 4;
            $this->size = $files[$nesting[0]]['size'][$nesting[1]] ?? 0;
        } else {
            $this->name = $files[$fileName]['name'] ?? '';
            $this->type = $files[$fileName]['type'] ?? '';
            $this->tmpName = $files[$fileName]['tmp_name'] ?? '';
            $this->error = $files[$fileName]['error'] ?? 4;
            $this->size = $files[$fileName]['size'] ?? 0;
        }
        $this->isFile = (bool)$this->size;

        // Уникальное имя файла для сохранения
        $this->file_name_save = '';
    }

    public function save($folder = ''): bool|string
    {
        if (!$this->isFile) {
            return false;
        }

        $dir = UPLOADS;
        if ($folder) {
            $dir .= '/' . trim($folder, '/');
        }

        $dir .= '/' . date('Y') . '/' . date('m') . '/' . date('d');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $dir_url = str_replace(WWW, '', $dir);

        $file_url = $dir_url . '/' . $this->file_name_save;
        $file_path = $dir . '/' . $this->file_name_save;

        if (move_uploaded_file($this->tmpName, $file_path)) {
            return $file_url;
        }
        return false;
    }

    public function getSaveName(): bool|string 
    {
        if (!$this->isFile) {
            return false;
        }

        $this->file_name_save = md5($this->name . time() . uniqid('', true)) . '.' . $this->getExt();

        return $this->file_name_save;
    }

    public function getExt(): string
    {
        $file_ext = explode('.', $this->name);
        return end($file_ext);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTmpName(): string
    {
        return $this->tmpName;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public static function remove(string $filename): void
    {
        $filename = str_replace('..', '', $filename);
        if (file_exists($filename)) {
            @unlink($filename);
        }
    }


}