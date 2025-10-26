<?php

namespace App\Controller;

use App\Entity\Promotion;
use App\Form\PromotionType;
use App\Repository\PromotionRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/promotions')]
#[IsGranted('ROLE_ADMIN')]
class AdminPromotionController extends AbstractController
{
    #[Route('/', name: 'admin_promotion_index')]
    public function index(PromotionRepository $promotionRepository, OrderRepository $orderRepository): Response
    {
        $promotions = $promotionRepository->findAll();
        $recentOrders = $orderRepository->findRecentOrders(10);

        return $this->render('admin/promotion/index.html.twig', [
            'promotions' => $promotions,
            'recentOrders' => $recentOrders,
        ]);
    }

    #[Route('/new', name: 'admin_promotion_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, OrderRepository $orderRepository): Response
    {
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($promotion);
            $entityManager->flush();

            $this->addFlash('success', 'Promotion créée avec succès!');
            return $this->redirectToRoute('admin_promotion_index');
        }

        $recentOrders = $orderRepository->findRecentOrders(10);

        return $this->render('admin/promotion/new.html.twig', [
            'form' => $form->createView(),
            'recentOrders' => $recentOrders,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_promotion_edit')]
    public function edit(Promotion $promotion, Request $request, EntityManagerInterface $entityManager, OrderRepository $orderRepository): Response
    {
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Promotion modifiée avec succès!');
            return $this->redirectToRoute('admin_promotion_index');
        }

        $recentOrders = $orderRepository->findRecentOrders(10);

        return $this->render('admin/promotion/edit.html.twig', [
            'form' => $form->createView(),
            'promotion' => $promotion,
            'recentOrders' => $recentOrders,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_promotion_delete', methods: ['POST'])]
    public function delete(Promotion $promotion, EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->remove($promotion);
            $entityManager->flush();
            
            $this->addFlash('success', 'Promotion supprimée avec succès!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression de la promotion: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_promotion_index');
    }

    #[Route('/{id}/toggle', name: 'admin_promotion_toggle', methods: ['POST'])]
    public function toggle(Promotion $promotion, EntityManagerInterface $entityManager): Response
    {
        $promotion->setIsActive(!$promotion->isActive());
        $entityManager->flush();

        $status = $promotion->isActive() ? 'activée' : 'désactivée';
        $this->addFlash('success', "Promotion {$status} avec succès!");

        return $this->redirectToRoute('admin_promotion_index');
    }
}