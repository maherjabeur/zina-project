<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class AdminCategoryController extends AbstractController
{
    #[Route('/', name: 'admin_categories')]
    public function index(CategoryRepository $categoryRepository, OrderRepository $orderRepository): Response
    {
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categoryRepository->findWithProductCount(),
            'recentOrders' => $orderRepository->findRecentOrders(10),
        ]);
    }

    #[Route('/new', name: 'admin_category_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$category->getSlug()) {
                $category->setSlug($slugger->slug($category->getName())->lower());
            }

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Categorie creee avec succes!');
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
            if (!$category->getSlug()) {
                $category->setSlug($slugger->slug($category->getName())->lower());
            }

            $entityManager->flush();

            $this->addFlash('success', 'Categorie modifiee avec succes!');
            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function delete(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('admin_category_delete_' . $category->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisee.');
            return $this->redirectToRoute('admin_categories');
        }

        if ($category->getProducts()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette categorie car elle contient des produits.');
            return $this->redirectToRoute('admin_categories');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Categorie supprimee avec succes!');
        return $this->redirectToRoute('admin_categories');
    }

    #[Route('/{id}/toggle', name: 'admin_category_toggle', methods: ['POST'])]
    public function toggle(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('admin_category_toggle_' . $category->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisee.');
            return $this->redirectToRoute('admin_categories');
        }

        $category->setIsActive(!$category->isActive());
        $entityManager->flush();

        $status = $category->isActive() ? 'activee' : 'desactivee';
        $this->addFlash('success', "Categorie {$status} avec succes!");

        return $this->redirectToRoute('admin_categories');
    }
}
