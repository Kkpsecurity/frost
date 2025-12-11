<?php

namespace KKP\Laravel\Traits;

trait StoragePathTrait
{
    public function storagePath(string $base = 'validations'): string
    {
        return storage_path($base);
    }

    public function tempValidationPath(): string
    {
        return $this->ensurePath('validations/temp_validations');
    }

    public function ensureStoragePath(?string $subDir = null): string
    {
        return $this->ensurePath('validations' . ($subDir ? "/{$subDir}" : ''));
    }

    protected function ensurePath(string $relativePath): string
    {
        $fullPath = storage_path($relativePath);

        if (!is_dir($fullPath)) {
            $this->createDir($fullPath);
        }

        return $fullPath;
    }

    protected function createDir(string $dir): void
    {
        logger("Creating directory: {$dir}");

        if (!mkdir($dir, 0775, true) && !is_dir($dir)) {
            abort(500, "Could not create directory: {$dir}");
        }
    }
}

