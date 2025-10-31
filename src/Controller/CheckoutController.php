<?php
// src/Controller/CheckoutController.php
namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProductRepository;
use App\Repository\PromotionRepository;
use App\Repository\SettingRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'checkout')]
    public function index(
        SettingRepository $settingRepository,
        SessionInterface $session,
        ProductRepository $productRepository,
        PromotionRepository $promotionRepository
    ): Response {
        $cart = $session->get('cart', []);

        if (empty($cart)) {
            $this->addFlash('warning', 'Votre panier est vide');
            return $this->redirectToRoute('products');
        }

        $cartData = [];
        $total = 0;
        $totalDiscount = 0;
        $totalWithDiscount = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                // Vérifier le stock
                if ($product->getQuantity() < $quantity) {
                    $this->addFlash('warning', "Le produit \"{$product->getName()}\" n'est plus disponible en quantité suffisante. Stock restant: {$product->getQuantity()}");
                    return $this->redirectToRoute('cart');
                }

                // Trouver la meilleure promotion active pour ce produit
                $bestPromotion = $promotionRepository->findBestPromotionForProduct($product->getId());
                $discount = 0;
                $discountedPrice = $product->getPrice();

                if ($bestPromotion && $bestPromotion->isValid()) {
                    $discount = $bestPromotion->getDiscount();
                    $discountedPrice = $bestPromotion->calculateDiscountedPrice($product->getPrice());
                }

                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'discount' => $discount,
                    'discountedPrice' => $discountedPrice,
                    'promotion' => $bestPromotion
                ];

                $total += $product->getPrice() * $quantity;
                $totalDiscount += ($product->getPrice() - $discountedPrice) * $quantity;
                $totalWithDiscount += $discountedPrice * $quantity;
            }
        }

        $settings = $settingRepository->findOneBy([], ['id' => 'DESC']);
        $shippingFee = $settings ? $settings->getShippingFee() : 0;
        $finalTotal = $totalWithDiscount + $shippingFee;

        return $this->render('checkout/index.html.twig', [
            'shippingFee' => (float)$shippingFee,
            'cartData' => $cartData,
            'total' => (float)$total,
            'totalDiscount' => $totalDiscount,
            'totalWithDiscount' => $totalWithDiscount,
            'finalTotal' => $finalTotal,
        ]);
    }

    #[Route('/checkout/process', name: 'checkout_process', methods: ['POST'])]
    public function process(
        Request $request,
        SessionInterface $session,
        ProductRepository $productRepository,
        PromotionRepository $promotionRepository,
        EntityManagerInterface $entityManager,
        NotificationService $notificationService
    ): Response {
        $cart = $session->get('cart', []);

        if (empty($cart)) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('products');
        }

        // Validation des données du formulaire
        $customerName = $request->request->get('customer_name');
        $customerPhone = $request->request->get('customer_phone');
        $customerEmail = $request->request->get('customer_email');
        $shippingAddress = $request->request->get('shipping_address');

        if (empty($customerName) || empty($customerPhone) || empty($shippingAddress)) {
            $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires');
            return $this->redirectToRoute('checkout');
        }

        // Validation du téléphone
        if (!preg_match('/^[0-9]{8}$/', $customerPhone)) {
            $this->addFlash('error', 'Veuillez entrer un numéro de téléphone valide (8 chiffres)');
            return $this->redirectToRoute('checkout');
        }

        // Validation de l'email si fourni
        if ($customerEmail && !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Veuillez entrer un email valide ou laisser le champ vide');
            return $this->redirectToRoute('checkout');
        }

        // Vérifier à nouveau le stock avant de créer la commande
        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if (!$product || $product->getQuantity() < $quantity) {
                $this->addFlash('error', "Le produit \"{$product->getName()}\" n'est plus disponible en quantité suffisante");
                return $this->redirectToRoute('cart');
            }
        }

        // Créer la commande
        $order = new Order();
        $order->setCustomerName($customerName);
        $order->setCustomerPhone($customerPhone);
        $order->setCustomerEmail($customerEmail ?: null);
        $order->setShippingAddress($shippingAddress);

        if ($this->getUser()) {
            $order->setUser($this->getUser());
        }

        $total = 0;
        $totalDiscount = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $productRepository->find($productId);
            if ($product && $product->getQuantity() >= $quantity) {
                // Trouver la meilleure promotion active pour ce produit
                $bestPromotion = $promotionRepository->findBestPromotionForProduct($product->getId());
                $unitPrice = $product->getPrice();
                $discount = 0;

                if ($bestPromotion && $bestPromotion->isValid()) {
                    $discount = $bestPromotion->getDiscount();
                    $unitPrice = $bestPromotion->calculateDiscountedPrice($product->getPrice());
                }

                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);
                $orderItem->setUnitPrice($unitPrice);
                $orderItem->setOriginalPrice($product->getPrice());
                $orderItem->setDiscount($discount);

                if ($product->getSizes()->count() > 0) {
                    $sizes = [];
                    foreach ($product->getSizes() as $size) {
                        $sizes[] = $size->getName();
                    }
                    $orderItem->setSize(implode(', ', $sizes));
                }
                $orderItem->setColor($product->getColor());
                $orderItem->setOrder($order);

                $entityManager->persist($orderItem);
                $total += $unitPrice * $quantity;
                $totalDiscount += ($product->getPrice() - $unitPrice) * $quantity;

                // Mettre à jour le stock
                $product->setQuantity($product->getQuantity() - $quantity);
            }
        }

        $order->setTotal($total);
        $order->setDiscount($totalDiscount);
        $entityManager->persist($order);
        $entityManager->flush();

        // Envoyer les notifications seulement si l'email est valide
        if ($customerEmail && filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                $notificationService->sendOrderNotification($order);
            } catch (\Exception $e) {
                error_log('Erreur envoi email: ' . $e->getMessage());
                $this->addFlash('warning', 'Votre commande a été passée mais l\'email de confirmation n\'a pas pu être envoyé');
            }
        } else {
            // Pas d'email, on envoie seulement la notification admin
            try {
                $notificationService->sendAdminNotification($order);
            } catch (\Exception $e) {
                error_log('Erreur envoi notification admin: ' . $e->getMessage());
            }
        }

        // Vider le panier
        $session->remove('cart');

        $this->addFlash('success', 'Votre commande a été passée avec succès !' .
            ($customerEmail ? ' Vous recevrez un email de confirmation.' : ' Votre numéro de téléphone a été enregistré.'));

        return $this->redirectToRoute('checkout_success', ['id' => $order->getId()]);
    }

    #[Route('/checkout/success/{id}', name: 'checkout_success')]
    public function success(Order $order): Response
    {
        return $this->render('checkout/success.html.twig', [
            'order' => $order
        ]);
    }
}