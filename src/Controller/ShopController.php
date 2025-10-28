<?php
// src/Controller/ShopController.php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\Order;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\PromotionRepository;
use App\Repository\SliderImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SizeRepository;
use Knp\Component\Pager\PaginatorInterface;

class ShopController extends AbstractController
{
// new 

 #[Route('/', name: 'home')]
    public function index(ProductRepository $productRepository, SliderImageRepository $sliderImageRepository, PromotionRepository $promotionRepository): Response
    {
        $featuredProducts = $productRepository->findFeaturedHome(12);
        $sliderImages = $sliderImageRepository->findActiveSlides();

        // Préparer les données avec les promotions
        $productsWithPromotions = [];
        foreach ($featuredProducts as $product) {
            $bestPromotion = $promotionRepository->findBestPromotionForProduct($product->getId());
            $productsWithPromotions[] = [
                'product' => $product,
                'promotion' => $bestPromotion
            ];
        }

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
        
        // Create the base query
        $query = $productRepository->findPaginated($page, 12, $categorySlug, $sizeCode);
        
        // Paginate results
        $products = $paginator->paginate(
            $query,
            $page,
            10 // limit per page
        );
        
        // Préparer les données avec les promotions
        $productsWithPromotions = [];
        foreach ($products as $product) {
            $bestPromotion = $promotionRepository->findBestPromotionForProduct($product->getId());
            $productsWithPromotions[] = [
                'product' => $product,
                'promotion' => $bestPromotion
            ];
        }

        $categories = $categoryRepository->findActiveCategories();
        $sizes = $sizeRepository->findActiveSizes();
        
        return $this->render('shop/products.html.twig', [
            'productsWithPromotions' => $productsWithPromotions,
            'categories' => $categories,
            'sizes' => $sizes,
            'currentCategory' => $categorySlug,
            'currentSize' => $sizeCode,
            'products' => $products, // Garder pour la pagination
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


// old  
   
    #[Route('/search/quick', name: 'search_quick_redirect')]
    public function quickSearchRedirect(Request $request): Response
    {
        $query = $request->query->get('q', '');

        if (!empty($query)) {
            return $this->redirectToRoute('search', ['q' => $query]);
        }

        return $this->redirectToRoute('products');
    }
}
