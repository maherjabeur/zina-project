<?php
// src/Controller/CartController.php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\Settings;
use App\Repository\ProductRepository;
use App\Repository\PromotionRepository;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]
    public function index(
        SettingRepository $settingRepository,
        SessionInterface $session,
        ProductRepository $productRepository,
        PromotionRepository $promotionRepository
    ): Response {
        $cart = $session->get('cart', []);
        $cartData = [];
        $total = 0;
        $totalDiscount = 0;
        $totalWithDiscount = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
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

        return $this->render('cart/index.html.twig', [
            'shippingFee' => $shippingFee,
            'cartData' => $cartData,
            'total' => $total,
            'totalDiscount' => $totalDiscount,
            'totalWithDiscount' => $totalWithDiscount,
            'finalTotal' => $finalTotal,
        ]);
    }


    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(Product $product, Request $request, SessionInterface $session): Response
    {
        $quantity = (int) $request->request->get('quantity', 1);
        $cart = $session->get('cart', []);

        $id = $product->getId();
        if (isset($cart[$id])) {
            $cart[$id] += $quantity;
        } else {
            $cart[$id] = $quantity;
        }

        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit ajouté au panier !');

        // Rediriger vers la page précédente ou vers le panier
        $referer = $request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove(Product $product, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $id = $product->getId();

        if (isset($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit retiré du panier !');

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/update', name: 'cart_update', methods: ['POST'])]
    public function update(Request $request, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $productId = $data['productId'] ?? null;
        $quantity = (int) ($data['quantity'] ?? 1);

        $cart = $session->get('cart', []);

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $session->set('cart', $cart);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('cart');

        $this->addFlash('success', 'Panier vidé !');

        return $this->redirectToRoute('cart');
    }

    /**
     * Affiche un mini-panier pour AJAX
     */
    #[Route('/cart/mini', name: 'cart_mini')]
    public function miniCart(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $cartData = [];
        $total = 0;
        $itemCount = 0;

        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);
            if ($product) {
                $cartData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                $total += $product->getPrice() * $quantity;
                $itemCount += $quantity;
            }
        }

        return $this->render('cart/_mini_cart.html.twig', [
            'cartData' => $cartData,
            'total' => $total,
            'itemCount' => $itemCount
        ]);
    }
}