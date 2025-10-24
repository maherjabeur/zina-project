<?php
// src/Controller/AdminSizeController.php
namespace App\Controller;

use App\Entity\Size;
use App\Form\SizeType;
use App\Repository\OrderRepository;
use App\Repository\SizeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin/sizes')]
#[IsGranted('ROLE_ADMIN')]
class AdminSizeController extends AbstractController
{
    #[Route('/', name: 'admin_sizes')]
    public function index(SizeRepository $sizeRepository,OrderRepository $orderRepository): Response
    {
        $sizes = $sizeRepository->findWithProductCount();
        $recentOrders = $orderRepository->findRecentOrders(10);

        return $this->render('admin/sizes/index.html.twig', [
            'sizes' => $sizes,
            'recentOrders' => $recentOrders,
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

            $this->addFlash('success', 'Taille créée avec succès!');
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

            $this->addFlash('success', 'Taille modifiée avec succès!');
            return $this->redirectToRoute('admin_sizes');
        }

        return $this->render('admin/sizes/edit.html.twig', [
            'form' => $form->createView(),
            'size' => $size,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_size_delete', methods: ['POST'])]
    public function delete(Size $size, EntityManagerInterface $entityManager): Response
    {
        // Vérifier s'il y a des produits utilisant cette taille
        if ($size->getProducts()->count() > 0) {
            $this->addFlash('error', 'Impossible de supprimer cette taille car elle est utilisée par des produits.');
            return $this->redirectToRoute('admin_sizes');
        }

        $entityManager->remove($size);
        $entityManager->flush();

        $this->addFlash('success', 'Taille supprimée avec succès!');
        return $this->redirectToRoute('admin_sizes');
    }

    #[Route('/{id}/toggle', name: 'admin_size_toggle', methods: ['POST'])]
    public function toggle(Size $size, EntityManagerInterface $entityManager): Response
    {
        $size->setIsActive(!$size->isActive());
        $entityManager->flush();

        $status = $size->isActive() ? 'activée' : 'désactivée';
        $this->addFlash('success', "Taille {$status} avec succès!");

        return $this->redirectToRoute('admin_sizes');
    }
}