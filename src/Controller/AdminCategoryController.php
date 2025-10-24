<?php
// src/Controller/AdminCategoryController.php
namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class AdminCategoryController extends AbstractController
{
    #[Route('/', name: 'admin_categories')]
    public function index(CategoryRepository $categoryRepository, OrderRepository $orderRepository): Response
    {
        $categories = $categoryRepository->findWithProductCount();
        $recentOrders = $orderRepository->findRecentOrders(10);

        
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories,
            'recentOrders' => $recentOrders,
        ]);
    }

    #[Route('/new', name: 'admin_category_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le slug si vide
            if (!$category->getSlug()) {
                $slug = $slugger->slug($category->getName())->lower();
                $category->setSlug($slug);
            }

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Catégorie créée avec succès!');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categories/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_category_edit')]
    public function edit(Category $category, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le slug si vide
            if (!$category->getSlug()) {
                $slug = $slugger->slug($category->getName())->lower();
                $category->setSlug($slug);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Catégorie modifiée avec succès!');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function delete(Category $category, EntityManagerInterface $entityManager): Response
    {
        // Vérifier s'il y a des produits dans cette catégorie
        if ($category->getProducts()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette catégorie car elle contient des produits.');
            return $this->redirectToRoute('admin_categories');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Catégorie supprimée avec succès!');
        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/{id}/toggle', name: 'admin_category_toggle', methods: ['POST'])]
    public function toggle(Category $category, EntityManagerInterface $entityManager): Response
    {
        $category->setIsActive(!$category->isActive());
        $entityManager->flush();

        $status = $category->isActive() ? 'activée' : 'désactivée';
        $this->addFlash('success', "Catégorie {$status} avec succès!");

        return $this->redirectToRoute('admin_categories');
    }
}