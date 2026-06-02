<?php
// src/Service/ProductImageUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductImageUploader
{
    private $targetDirectory;
    private $slugger;
    private WebpImageConverter $webpImageConverter;

    public function __construct($targetDirectory, SluggerInterface $slugger, WebpImageConverter $webpImageConverter)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
        $this->webpImageConverter = $webpImageConverter;
    }

    public function upload(UploadedFile $file): string
    {
        // Vérifier que le dossier existe, sinon le créer
        if (!is_dir($this->targetDirectory)) {
            mkdir($this->targetDirectory, 0777, true);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        try {
            $fileName = $this->webpImageConverter->convertUploadedFile($file, $this->getTargetDirectory(), $safeFilename);
        } catch (FileException|\RuntimeException $e) {
            throw new \Exception('Erreur lors de l\'upload du fichier: '.$e->getMessage());
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function delete(string $filename): bool
    {
        if (!$filename) {
            return false;
        }
        
        $filePath = $this->getTargetDirectory().'/'.$filename;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
}
