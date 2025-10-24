<?php
// src/Controller/AdminController.php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\Order;
use App\Form\ProductType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(OrderRepository $orderRepository): Response
    {
        $pendingOrders = $orderRepository->findBy(['status' => 'pending']);
        $recentOrders = $orderRepository->findRecentOrders(10);

        return $this->render('admin/dashboard.html.twig', [
            'pendingOrders' => $pendingOrders,
            'recentOrders' => $recentOrders,
        ]);
    }

    #[Route('/products', name: 'admin_products')]
    public function products(Request $request, ProductRepository $productRepository, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search');
        
        // Créer la requête de base
        $queryBuilder = $productRepository->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC');

        // Ajouter la recherche si spécifiée
        if ($search) {
            $queryBuilder
                ->andWhere('p.name LIKE :search OR p.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Paginer les résultats
        $products = $paginator->paginate(
            $queryBuilder,
            $page,
            5 // limite par page
        );

        return $this->render('admin/products/index.html.twig', [
            'products' => $products,
            'search' => $search,
        ]);
    }

    #[Route('/product/new', name: 'admin_product_new')]
    public function newProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit créé avec succès!');
            return $this->redirectToRoute('admin_product_images', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('admin/products/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/orders', name: 'admin_orders')]
    public function orders(Request $request, OrderRepository $orderRepository, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);
        $status = $request->query->get('status');
        $search = $request->query->get('search');
        
        // Créer la requête de base
        $queryBuilder = $orderRepository->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC');
    
        // Filtre par statut
        if ($status) {
            $queryBuilder
                ->andWhere('o.status = :status')
                ->setParameter('status', $status);
        }
    
        // Recherche
        if ($search) {
            $queryBuilder
                ->andWhere('o.orderNumber LIKE :search OR o.customerName LIKE :search OR o.customerEmail LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
    
        // Paginer les résultats
        $orders = $paginator->paginate(
            $queryBuilder,
            $page,
            10 // limite par page
        );
    
        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orders,
            'current_status' => $status,
            'search' => $search,
        ]);
    }

    #[Route('/order/{id}/update-status', name: 'admin_order_update_status', methods: ['POST'])]
    public function updateOrderStatus(
        Order $order,
        Request $request,
        EntityManagerInterface $entityManager,
        NotificationService $notificationService
    ): Response {
        $newStatus = $request->request->get('status');

        // Validation du statut
        $validStatuses = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];

        if (!$newStatus || !in_array($newStatus, $validStatuses)) {
            $this->addFlash('error', 'Statut invalide');
            return $this->redirectToRoute('admin_orders');
        }

        $oldStatus = $order->getStatus();
        $order->setStatus($newStatus);

        // Envoyer une notification si c'est une nouvelle commande
        if (!$order->isNotified() && $newStatus === 'confirmed') {
            try {
                $notificationService->sendOrderNotification($order);
                $order->setNotified(true);
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Commande mise à jour mais erreur lors de l\'envoi de l\'email: ' . $e->getMessage());
            }
        }

        // Envoyer une notification de mise à jour de statut
        if ($oldStatus !== $newStatus) {
            try {
                $notificationService->sendStatusUpdateNotification($order);
            } catch (\Exception $e) {
                // Ne pas bloquer la mise à jour si l'email échoue
                error_log('Erreur envoi email statut: ' . $e->getMessage());
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'Statut de commande mis à jour!');
        return $this->redirectToRoute('admin_orders');
    }

    #[Route('/order/{id}', name: 'admin_order_show')]
    public function showOrder(Order $order): Response
    {
        return $this->render('admin/orders/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/order/{id}/delete', name: 'admin_order_delete', methods: ['POST'])]
    public function deleteOrder(Order $order, EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->remove($order);
            $entityManager->flush();
            $this->addFlash('success', 'Commande supprimée avec succès!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression de la commande: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_orders');
    }


    #[Route('/product/{id}/edit', name: 'admin_product_edit')]
    public function editProduct(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès!');
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/products/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/toggle', name: 'admin_product_toggle', methods: ['POST'])]
    public function toggleProduct(Product $product, EntityManagerInterface $entityManager): Response
    {
        // Utilisez soit getIsActive() soit isActive()
        $product->setIsActive(!$product->isActive());
        $entityManager->flush();

        $status = $product->isActive() ? 'activé' : 'désactivé';
        $this->addFlash('success', "Produit {$status} avec succès!");

        return $this->redirectToRoute('admin_products');
    }

    #[Route('/product/{id}/delete', name: 'admin_product_delete', methods: ['POST'])]
    public function deleteProduct(Product $product, EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->remove($product);
            $entityManager->flush();
            $this->addFlash('success', 'Produit supprimé avec succès!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la suppression du produit: ' . $e->getMessage());
        }

        return $this->redirectToRoute('admin_products');
    }
}