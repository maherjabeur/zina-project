<?php
// src/Controller/SearchController.php
namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function search(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $query = $request->query->get('q', '');
        $category = $request->query->get('category');
        
        if (empty($query)) {
            return $this->redirectToRoute('products');
        }

        $products = $productRepository->search($query, $category);
        $categories = $categoryRepository->findActiveCategories();

        return $this->render('search/results.html.twig', [
            'products' => $products,
            'query' => $query,
            'categories' => $categories,
            'currentCategory' => $category,
            'resultsCount' => count($products)
        ]);
    }

    #[Route('/search/suggestions', name: 'search_suggestions')]
    public function suggestions(Request $request, ProductRepository $productRepository): JsonResponse
    {
        $query = $request->query->get('q', '');
        
        if (strlen($query) < 2) {
            return new JsonResponse([]);
        }

        $suggestions = $productRepository->findSearchSuggestions($query);
        
        $results = array_map(function($item) {
            return $item['name'];
        }, $suggestions);

        return new JsonResponse($results);
    }

    #[Route('/search/quick', name: 'search_quick', methods: ['POST'])]
    public function quickSearch(Request $request): Response
    {
        $query = $request->request->get('query', '');
        
        if (!empty($query)) {
            return $this->redirectToRoute('search', ['q' => $query]);
        }

        return $this->redirectToRoute('products');
    }
}