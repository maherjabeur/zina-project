<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\PromotionRepository;
use App\Repository\SizeRepository;
use App\Repository\SliderImageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        ProductRepository $productRepository,
        SliderImageRepository $sliderImageRepository,
        PromotionRepository $promotionRepository
    ): Response {
        $featuredProducts = $productRepository->findFeaturedHome(12);
        $sliderImages = $sliderImageRepository->findActiveSlides();
        $productsWithPromotions = $this->buildProductsWithPromotions($featuredProducts, $promotionRepository);

        return $this->render('shop/index.html.twig', [
            'productsWithPromotions' => $productsWithPromotions,
            'slider_images' => $sliderImages,
        ]);
    }

    #[Route('/products', name: 'products')]
    public function products(
        Request $request,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        SizeRepository $sizeRepository,
        PromotionRepository $promotionRepository,
        PaginatorInterface $paginator
    ): Response {
        $categorySlug = $request->query->get('category');
        $sizeCode = $request->query->get('size');
        $page = $request->query->getInt('page', 1);

        $query = $productRepository->findPaginated($page, 12, $categorySlug, $sizeCode);
        $products = $paginator->paginate($query, $page, 10);
        $productsWithPromotions = $this->buildProductsWithPromotions($products, $promotionRepository);

        return $this->render('shop/products.html.twig', [
            'productsWithPromotions' => $productsWithPromotions,
            'categories' => $categoryRepository->findActiveCategories(),
            'categoryProductCounts' => $categoryRepository->countActiveProductsBySlug(),
            'sizes' => $sizeRepository->findActiveSizes(),
            'currentCategory' => $categorySlug,
            'currentSize' => $sizeCode,
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'product_detail')]
    public function productDetail(Product $product, PromotionRepository $promotionRepository): Response
    {
        $bestPromotion = $promotionRepository->findBestPromotionForProduct($product->getId());
        $allPromotions = $promotionRepository->findActivePromotionsForProduct($product->getId());

        return $this->render('shop/product_detail.html.twig', [
            'product' => $product,
            'bestPromotion' => $bestPromotion,
            'allPromotions' => $allPromotions,
        ]);
    }

    #[Route('/search/quick', name: 'search_quick_redirect', methods: ['GET'])]
    public function quickSearchRedirect(Request $request): Response
    {
        $query = $request->query->get('q', '');

        if ($query !== '') {
            return $this->redirectToRoute('search', ['q' => $query]);
        }

        return $this->redirectToRoute('products');
    }

    private function buildProductsWithPromotions(iterable $products, PromotionRepository $promotionRepository): array
    {
        $productsArray = is_array($products) ? $products : iterator_to_array($products, false);
        $promotionsByProductId = $promotionRepository->findBestPromotionsForProducts($productsArray);

        $productsWithPromotions = [];
        foreach ($productsArray as $product) {
            $productsWithPromotions[] = [
                'product' => $product,
                'promotion' => $promotionsByProductId[$product->getId()] ?? null,
            ];
        }

        return $productsWithPromotions;
    }
}
