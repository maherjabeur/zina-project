<?php

namespace App\Controller;

use App\Entity\Size;
use App\Form\SizeType;
use App\Repository\OrderRepository;
use App\Repository\SizeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/sizes')]
#[IsGranted('ROLE_ADMIN')]
class AdminSizeController extends AbstractController
{
    #[Route('/', name: 'admin_sizes')]
    public function index(SizeRepository $sizeRepository, OrderRepository $orderRepository): Response
    {
        return $this->render('admin/sizes/index.html.twig', [
            'sizes' => $sizeRepository->findWithProductCount(),
            'recentOrders' => $orderRepository->findRecentOrders(10),
        ]);
    }

    #[Route('/new', name: 'admin_size_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $size = new Size();
        $form = $this->createForm(SizeType::class, $size);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($size);
            $entityManager->flush();

            $this->addFlash('success', 'Taille creee avec succes!');
            return $this->redirectToRoute('admin_sizes');
        }

        return $this->render('admin/sizes/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_size_edit')]
    public function edit(Size $size, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SizeType::class, $size);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Taille modifiee avec succes!');
            return $this->redirectToRoute('admin_sizes');
        }

        return $this->render('admin/sizes/edit.html.twig', [
            'form' => $form->createView(),
            'size' => $size,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_size_delete', methods: ['POST'])]
    public function delete(Size $size, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('admin_size_delete_' . $size->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisee.');
            return $this->redirectToRoute('admin_sizes');
        }

        if ($size->getProducts()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette taille car elle est utilisee par des produits.');
            return $this->redirectToRoute('admin_sizes');
        }

        $entityManager->remove($size);
        $entityManager->flush();

        $this->addFlash('success', 'Taille supprimee avec succes!');
        return $this->redirectToRoute('admin_sizes');
    }

    #[Route('/{id}/toggle', name: 'admin_size_toggle', methods: ['POST'])]
    public function toggle(Size $size, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('admin_size_toggle_' . $size->getId(), (string) $request->request->get('_token'))) {
            $this->addFlash('error', 'Action non autorisee.');
            return $this->redirectToRoute('admin_sizes');
        }

        $size->setIsActive(!$size->isActive());
        $entityManager->flush();

        $status = $size->isActive() ? 'activee' : 'desactivee';
        $this->addFlash('success', "Taille {$status} avec succes!");

        return $this->redirectToRoute('admin_sizes');
    }
}
