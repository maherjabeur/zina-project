<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class WebpImageConverter
{
    public function convertUploadedFile(UploadedFile $file, string $targetDirectory, string $baseFilename, int $quality = 82): string
    {
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        $filename = $baseFilename.'-'.uniqid().'.webp';
        $targetPath = rtrim($targetDirectory, '/\\').DIRECTORY_SEPARATOR.$filename;

        $this->convertFile($file->getPathname(), $targetPath, $quality);

        return $filename;
    }

    public function convertFile(string $sourcePath, string $targetPath, int $quality = 82): void
    {
        if (!is_file($sourcePath)) {
            throw new \RuntimeException(sprintf('Image introuvable: %s', $sourcePath));
        }

        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            throw new \RuntimeException(sprintf('Fichier image invalide: %s', $sourcePath));
        }

        $image = match ($imageInfo[2]) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => $this->createPngImage($sourcePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
            default => throw new \RuntimeException('Format non supporte. Utilisez JPEG, PNG ou WebP.'),
        };

        if (!$image) {
            throw new \RuntimeException(sprintf('Impossible de lire l image: %s', $sourcePath));
        }

        $targetDirectory = dirname($targetPath);
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0777, true);
        }

        if (!imagewebp($image, $targetPath, $quality)) {
            imagedestroy($image);
            throw new \RuntimeException(sprintf('Impossible de convertir l image en WebP: %s', $sourcePath));
        }

        imagedestroy($image);
    }

    private function createPngImage(string $sourcePath): \GdImage|false
    {
        $image = imagecreatefrompng($sourcePath);
        if (!$image) {
            return false;
        }

        if (!imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }
}
