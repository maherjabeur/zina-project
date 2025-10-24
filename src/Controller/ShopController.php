<?php
// src/Controller/ShopController.php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\Order;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
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
    #[Route('/', name: 'home')]
    public function index(ProductRepository $productRepository, SliderImageRepository $sliderImageRepository): Response
    {
        $featuredProducts = $productRepository->findFeaturedHome(8);
        $sliderImages = $sliderImageRepository->findActiveSlides();

        return $this->render('shop/index.html.twig', [
            'featuredProducts' => $featuredProducts,
            'slider_images' => $sliderImages,
        ]);
    }

    #[Route('/products', name: 'products')]
    public function products(
        Request $request,
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        SizeRepository $sizeRepository,
        PaginatorInterface $paginator
    ): Response {
        $categorySlug = $request->query->get('category');
        $sizeCode = $request->query->get('size');
        $page = $request->query->getInt('page', 1);
        
        // Créer la requête de base
        $query = $productRepository->findPaginated($page, 12, $categorySlug, $sizeCode);
        
        // Paginer les résultats
        $products = $paginator->paginate(
            $query,
            $page,
            12 // limite par page
        );
        
        $categories = $categoryRepository->findActiveCategories();
        $sizes = $sizeRepository->findActiveSizes();
        
        return $this->render('shop/products.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'sizes' => $sizes,
            'currentCategory' => $categorySlug,
            'currentSize' => $sizeCode,
        ]);
    }

    #[Route('/product/{id}', name: 'product_detail')]
    public function productDetail(Product $product): Response
    {
        return $this->render('shop/product_detail.html.twig', [
            'product' => $product,
        ]);
    }
    
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
