<?php

namespace App\Command;

use App\Entity\ProductImage;
use App\Entity\Settings;
use App\Entity\SliderImage;
use App\Service\WebpImageConverter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:images:convert-webp',
    description: 'Convertit les images du site en WebP et met a jour les references en base.',
)]
class ConvertImagesToWebpCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly WebpImageConverter $webpImageConverter,
        private readonly string $projectDir,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $publicDir = $this->projectDir.DIRECTORY_SEPARATOR.'public';
        $placeholderPath = $publicDir.'/images/placeholder.webp';

        $this->createPlaceholder($placeholderPath);

        $updatedProducts = $this->convertEntityImages(
            ProductImage::class,
            $publicDir.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'products',
            $placeholderPath
        );
        $updatedSliders = $this->convertEntityImages(
            SliderImage::class,
            $publicDir.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'slider',
            $placeholderPath
        );

        $this->convertStaticImage($publicDir.'/logo/logo.png', $publicDir.'/logo/logo.webp');
        $this->convertStaticImage($publicDir.'/logo/logo_foot.png', $publicDir.'/logo/logo_foot.webp');
        $this->convertStaticImage($publicDir.'/uploads/slider/Soldes-6911b4f6aa588.jpg', $publicDir.'/uploads/slider/Soldes-6911b4f6aa588.webp');
        $updatedSettings = $this->convertSettingsSeoImages($publicDir);

        $this->entityManager->flush();

        $io->success(sprintf(
            'Images converties en WebP. Produits mis a jour: %d. Slider mis a jour: %d. Reglages SEO mis a jour: %d.',
            $updatedProducts,
            $updatedSliders,
            $updatedSettings
        ));

        return Command::SUCCESS;
    }

    private function convertEntityImages(string $entityClass, string $directory, string $placeholderPath): int
    {
        $updated = 0;
        $repository = $this->entityManager->getRepository($entityClass);

        foreach ($repository->findAll() as $image) {
            $filename = $image->getFilename();
            if (!$filename || strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'webp') {
                continue;
            }

            $sourcePath = $directory.DIRECTORY_SEPARATOR.$filename;
            $webpFilename = pathinfo($filename, PATHINFO_FILENAME).'.webp';
            $webpPath = $directory.DIRECTORY_SEPARATOR.$webpFilename;

            if (!is_file($webpPath)) {
                if (is_file($sourcePath)) {
                    $this->webpImageConverter->convertFile($sourcePath, $webpPath);
                } else {
                    if (!is_dir($directory)) {
                        mkdir($directory, 0777, true);
                    }

                    copy($placeholderPath, $webpPath);
                }
            }

            $image->setFilename($webpFilename);
            $updated++;
        }

        return $updated;
    }

    private function convertStaticImage(string $sourcePath, string $targetPath): void
    {
        if (!is_file($sourcePath) || is_file($targetPath)) {
            return;
        }

        $this->webpImageConverter->convertFile($sourcePath, $targetPath);
    }

    private function convertSettingsSeoImages(string $publicDir): int
    {
        $updated = 0;
        $settingsRepository = $this->entityManager->getRepository(Settings::class);

        foreach ($settingsRepository->findAll() as $settings) {
            $seoImage = trim((string) $settings->getSeoImage());
            if ($seoImage === '' || str_starts_with($seoImage, 'http') || str_ends_with(strtolower($seoImage), '.webp')) {
                continue;
            }

            $relativePath = ltrim(str_replace('\\', '/', $seoImage), '/');
            $webpRelativePath = preg_replace('/\.[^.\/]+$/', '.webp', $relativePath) ?: 'logo/logo.webp';
            $sourcePath = $publicDir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $targetPath = $publicDir.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $webpRelativePath);

            if (is_file($sourcePath) && !is_file($targetPath)) {
                $this->webpImageConverter->convertFile($sourcePath, $targetPath);
            }

            if (is_file($targetPath)) {
                $settings->setSeoImage($webpRelativePath);
                $updated++;
            }
        }

        return $updated;
    }

    private function createPlaceholder(string $targetPath): void
    {
        if (is_file($targetPath)) {
            return;
        }

        $directory = dirname($targetPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $image = imagecreatetruecolor(900, 900);
        $background = imagecolorallocate($image, 255, 247, 249);
        $line = imagecolorallocate($image, 234, 221, 227);
        $primary = imagecolorallocate($image, 178, 58, 99);
        $muted = imagecolorallocate($image, 123, 113, 122);

        imagefill($image, 0, 0, $background);
        imagerectangle($image, 58, 58, 842, 842, $line);
        imagefilledellipse($image, 450, 380, 180, 180, $line);
        imagefilledrectangle($image, 285, 555, 715, 585, $line);
        imagestring($image, 5, 342, 655, 'Bella Couture', $primary);
        imagestring($image, 3, 340, 690, 'Image non disponible', $muted);

        imagewebp($image, $targetPath, 82);
        imagedestroy($image);
    }
}
