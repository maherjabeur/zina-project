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
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('products');
        }

        $cartData = [];
        $total = 0;
        $totalDiscount = 0;
        $totalWithDiscount = 0;

        foreach ($cart as $key => $rawItem) {
            $item = $this->normalizeCartItem($key, $rawItem);
            if (!$item) {
                continue;
            }

            if (!$item['size'] || !$item['color']) {
                $this->addFlash('warning', 'Veuillez choisir une taille et une couleur pour chaque article.');
                return $this->redirectToRoute('cart');
            }

            $product = $productRepository->find($item['productId']);
            if (!$product) {
                continue;
            }

            if ($product->getQuantity() < $item['quantity']) {
                $this->addFlash('warning', "Le produit \"{$product->getName()}\" n est plus disponible en quantite suffisante. Stock restant: {$product->getQuantity()}");
                return $this->redirectToRoute('cart');
            }

            $bestPromotion = $promotionRepository->findBestPromotionForProduct($product->getId());
            $discount = 0;
            $discountedPrice = $product->getPrice();

            if ($bestPromotion && $bestPromotion->isValid()) {
                $discount = $bestPromotion->getDiscount();
                $discountedPrice = $bestPromotion->calculateDiscountedPrice($product->getPrice());
            }

            $cartData[] = [
                'key' => $item['key'],
                'product' => $product,
                'quantity' => $item['quantity'],
                'size' => $item['size'],
                'color' => $item['color'],
                'discount' => $discount,
                'discountedPrice' => $discountedPrice,
                'promotion' => $bestPromotion,
            ];

            $total += $product->getPrice() * $item['quantity'];
            $totalDiscount += ($product->getPrice() - $discountedPrice) * $item['quantity'];
            $totalWithDiscount += $discountedPrice * $item['quantity'];
        }

        if (empty($cartData)) {
            $this->addFlash('warning', 'Votre panier ne contient aucun produit disponible.');
            return $this->redirectToRoute('cart');
        }

        $settings = $settingRepository->findOneBy([], ['id' => 'DESC']);
        $shippingFee = $settings ? $settings->getShippingFee() : 0;
        $finalTotal = $totalWithDiscount + $shippingFee;

        return $this->render('checkout/index.html.twig', [
            'shippingFee' => (float) $shippingFee,
            'cartData' => $cartData,
            'total' => (float) $total,
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
        SettingRepository $settingRepository,
        EntityManagerInterface $entityManager,
        NotificationService $notificationService
    ): Response {
        $cart = $session->get('cart', []);

        if (empty($cart)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('products');
        }

        $customerName = trim((string) $request->request->get('customer_name'));
        $customerPhone = trim((string) $request->request->get('customer_phone'));
        $customerEmail = trim((string) $request->request->get('customer_email'));
        $shippingAddress = trim((string) $request->request->get('shipping_address'));

        if ($customerName === '' || $customerPhone === '' || $shippingAddress === '') {
            $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
            return $this->redirectToRoute('checkout');
        }

        if (!preg_match('/^[0-9]{8}$/', $customerPhone)) {
            $this->addFlash('error', 'Veuillez entrer un numero de telephone valide (8 chiffres).');
            return $this->redirectToRoute('checkout');
        }

        if ($customerEmail !== '' && !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('error', 'Veuillez entrer un email valide ou laisser le champ vide.');
            return $this->redirectToRoute('checkout');
        }

        $cartItems = [];
        foreach ($cart as $key => $rawItem) {
            $item = $this->normalizeCartItem($key, $rawItem);
            if (!$item) {
                continue;
            }

            if (!$item['size'] || !$item['color']) {
                $this->addFlash('error', 'Une taille et une couleur sont obligatoires pour chaque article.');
                return $this->redirectToRoute('cart');
            }

            $product = $productRepository->find($item['productId']);
            if (!$product) {
                $this->addFlash('error', 'Un produit de votre panier n est plus disponible.');
                return $this->redirectToRoute('cart');
            }

            if ($product->getQuantity() < $item['quantity']) {
                $this->addFlash('error', "Le produit \"{$product->getName()}\" n est plus disponible en quantite suffisante.");
                return $this->redirectToRoute('cart');
            }

            $cartItems[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'size' => $item['size'],
                'color' => $item['color'],
            ];
        }

        if (empty($cartItems)) {
            $this->addFlash('error', 'Votre panier ne contient aucun produit disponible.');
            return $this->redirectToRoute('cart');
        }

        $order = new Order();
        $order->setCustomerName($customerName);
        $order->setCustomerPhone($customerPhone);
        $order->setCustomerEmail($customerEmail !== '' ? $customerEmail : null);
        $order->setShippingAddress($shippingAddress);

        if ($this->getUser()) {
            $order->setUser($this->getUser());
        }

        $settings = $settingRepository->findOneBy([], ['id' => 'DESC']);
        $shippingFee = $settings ? (float) $settings->getShippingFee() : 0.0;
        $itemsTotal = 0;
        $originalTotal = 0;
        $totalDiscount = 0;

        foreach ($cartItems as $item) {
            $product = $item['product'];
            $quantity = $item['quantity'];

            $bestPromotion = $promotionRepository->findBestPromotionForProduct($product->getId());
            $originalPrice = (float) $product->getPrice();
            $unitPrice = $originalPrice;
            $discount = 0;

            if ($bestPromotion && $bestPromotion->isValid()) {
                $discount = $bestPromotion->getDiscount();
                $unitPrice = (float) $bestPromotion->calculateDiscountedPrice($product->getPrice());
            }

            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantity);
            $orderItem->setUnitPrice(number_format($unitPrice, 2, '.', ''));
            $orderItem->setOriginalPrice($originalPrice);
            $orderItem->setDiscount($discount);
            $orderItem->setPromotionTitle($bestPromotion && $bestPromotion->isValid() ? $bestPromotion->getTitle() : null);
            $orderItem->setSize($item['size']);
            $orderItem->setColor($item['color']);
            $orderItem->setOrder($order);

            $entityManager->persist($orderItem);

            $itemsTotal += $unitPrice * $quantity;
            $originalTotal += $originalPrice * $quantity;
            $totalDiscount += ($originalPrice - $unitPrice) * $quantity;
            $product->setQuantity($product->getQuantity() - $quantity);
        }

        $finalTotal = $itemsTotal + $shippingFee;
        $order->setOriginalTotal($originalTotal);
        $order->setDiscount($totalDiscount);
        $order->setShippingFee($shippingFee);
        $order->setTotal(number_format($finalTotal, 2, '.', ''));
        $entityManager->persist($order);
        $entityManager->flush();

        if ($customerEmail !== '') {
            try {
                $notificationService->sendOrderNotification($order);
            } catch (\Exception $e) {
                error_log('Erreur envoi email: ' . $e->getMessage());
                $this->addFlash('warning', 'Votre commande a ete passee mais l email de confirmation n a pas pu etre envoye.');
            }
        } else {
            try {
                $notificationService->sendAdminNotification($order);
            } catch (\Exception $e) {
                error_log('Erreur envoi notification admin: ' . $e->getMessage());
            }
        }

        $session->remove('cart');

        $this->addFlash('success', 'Votre commande a ete passee avec succes !');

        return $this->redirectToRoute('checkout_success', ['id' => $order->getId()]);
    }

    #[Route('/checkout/success/{id}', name: 'checkout_success')]
    public function success(Order $order): Response
    {
        return $this->render('checkout/success.html.twig', [
            'order' => $order,
        ]);
    }

    private function normalizeCartItem(string|int $key, mixed $rawItem): ?array
    {
        if (is_array($rawItem) && isset($rawItem['productId'], $rawItem['quantity'])) {
            return [
                'key' => (string) $key,
                'productId' => (int) $rawItem['productId'],
                'quantity' => max(1, (int) $rawItem['quantity']),
                'size' => isset($rawItem['size']) ? trim((string) $rawItem['size']) : null,
                'color' => isset($rawItem['color']) ? trim((string) $rawItem['color']) : null,
            ];
        }

        if (is_numeric($key) && is_numeric($rawItem)) {
            return [
                'key' => (string) $key,
                'productId' => (int) $key,
                'quantity' => max(1, (int) $rawItem),
                'size' => null,
                'color' => null,
            ];
        }

        return null;
    }
}
