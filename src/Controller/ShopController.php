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

class ShopController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProductRepository $productRepository, SliderImageRepository $sliderImageRepository): Response
    {
        $featuredProducts = $productRepository->findBy(['isActive' => true], ['createdAt' => 'DESC'], 8);
        $sliderImages = $sliderImageRepository->findActiveSlides();
        
        return $this->render('shop/index.html.twig', [
            'featuredProducts' => $featuredProducts,
            'slider_images' => $sliderImages,
        ]);
    }

#[Route('/products', name: 'products')]
public function products(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
{
    $categorySlug = $request->query->get('category');
    $size = $request->query->get('size');
    
    if ($categorySlug) {
        $products = $productRepository->findByCategorySlug($categorySlug);
    } else {
        $products = $productRepository->findByFilters($categorySlug, $size);
    }
    
    $categories = $categoryRepository->findActiveCategories();
    
    return $this->render('shop/products.html.twig', [
        'products' => $products,
        'categories' => $categories,
        'currentCategory' => $categorySlug,
        'currentSize' => $size,
    ]);
}

    #[Route('/product/{id}', name: 'product_detail')]
    public function productDetail(Product $product): Response
    {
        return $this->render('shop/product_detail.html.twig', [
            'product' => $product,
        ]);
    }
}