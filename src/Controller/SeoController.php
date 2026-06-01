<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SeoController extends AbstractController
{
    #[Route('/robots.txt', name: 'seo_robots', defaults: ['_format' => 'txt'])]
    public function robots(Request $request, SettingRepository $settingRepository): Response
    {
        $settings = $settingRepository->getCurrentSettings();
        $host = $request->getSchemeAndHttpHost();

        $content = "User-agent: *\n";
        if ($settings && !$settings->isSeoIndexingEnabled()) {
            $content .= "Disallow: /\n";
        } else {
            $content .= "Disallow: /admin\n";
            $content .= "Disallow: /cart\n";
            $content .= "Disallow: /checkout\n";
            $content .= "Disallow: /search\n";
        }

        $content .= "\nSitemap: " . $host . $this->generateUrl('seo_sitemap') . "\n";

        return new Response($content, Response::HTTP_OK, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    #[Route('/sitemap.xml', name: 'seo_sitemap', defaults: ['_format' => 'xml'])]
    public function sitemap(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $host = $request->getSchemeAndHttpHost();
        $urls = [
            [$host . $this->generateUrl('home'), '1.0', 'daily', new \DateTime()],
            [$host . $this->generateUrl('products'), '0.9', 'daily', new \DateTime()],
            [$host . $this->generateUrl('contact'), '0.5', 'monthly', new \DateTime()],
        ];

        foreach ($categoryRepository->findBy(['isActive' => true], ['position' => 'ASC']) as $category) {
            $urls[] = [
                $host . $this->generateUrl('products', ['category' => $category->getSlug()]),
                '0.8',
                'weekly',
                $category->getUpdatedAt() ?? new \DateTime(),
            ];
        }

        foreach ($productRepository->findBy(['isActive' => true], ['createdAt' => 'DESC']) as $product) {
            $urls[] = [
                $host . $this->generateUrl('product_detail', ['id' => $product->getId()]),
                '0.8',
                'weekly',
                $product->getCreatedAt() ?? new \DateTime(),
            ];
        }

        $xml = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];

        foreach ($urls as [$loc, $priority, $changefreq, $lastmod]) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>' . htmlspecialchars($loc, ENT_XML1) . '</loc>';
            $xml[] = '    <lastmod>' . $lastmod->format('Y-m-d') . '</lastmod>';
            $xml[] = '    <changefreq>' . $changefreq . '</changefreq>';
            $xml[] = '    <priority>' . $priority . '</priority>';
            $xml[] = '  </url>';
        }

        $xml[] = '</urlset>';

        return new Response(implode("\n", $xml), Response::HTTP_OK, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
