<?php
// src/Controller/CartController.php
namespace App\Controller;

use App\Entity\Product;
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

        foreach ($cart as $key => $rawItem) {
            $item = $this->normalizeCartItem($key, $rawItem);
            if (!$item) {
                continue;
            }

            $product = $productRepository->find($item['productId']);
            if (!$product) {
                continue;
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
        if ((int) $product->getQuantity() <= 0) {
            $this->addFlash('warning', 'Ce produit est actuellement indisponible.');
            return $this->redirectToReferer($request);
        }

        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $quantity = min($quantity, (int) $product->getQuantity());

        $selectedSize = $this->resolveSelectedSize($product, $request->request->get('size'));
        $selectedColor = $this->resolveSelectedColor($product, $request->request->get('color'));

        if (!$selectedSize) {
            $this->addFlash('warning', 'Veuillez choisir une taille disponible avant d ajouter ce produit.');
            return $this->redirectToReferer($request);
        }

        if (!$selectedColor) {
            $this->addFlash('warning', 'Veuillez choisir une couleur disponible avant d ajouter ce produit.');
            return $this->redirectToReferer($request);
        }

        $cart = $session->get('cart', []);
        $cartKey = $this->buildCartKey($product, $selectedSize, $selectedColor);
        $currentQuantity = isset($cart[$cartKey]) && is_array($cart[$cartKey]) ? (int) $cart[$cartKey]['quantity'] : 0;

        if ($currentQuantity + $quantity > (int) $product->getQuantity()) {
            $this->addFlash('warning', sprintf(
                'Stock insuffisant pour %s en taille %s, couleur %s. Stock restant: %d.',
                $product->getName(),
                $selectedSize,
                $selectedColor,
                $product->getQuantity()
            ));
            return $this->redirectToReferer($request);
        }

        if (isset($cart[$cartKey]) && is_array($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'productId' => $product->getId(),
                'quantity' => $quantity,
                'size' => $selectedSize,
                'color' => $selectedColor,
            ];
        }

        $session->set('cart', $cart);

        $this->addFlash('success', sprintf(
            '%s ajoute au panier - Taille %s, couleur %s.',
            $product->getName(),
            $selectedSize,
            $selectedColor
        ));

        return $this->redirectToReferer($request);
    }

    #[Route('/cart/remove/{key}', name: 'cart_remove', requirements: ['key' => '.+'])]
    public function remove(string $key, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (isset($cart[$key])) {
            unset($cart[$key]);
        } elseif (ctype_digit($key)) {
            unset($cart[(int) $key], $cart[$key]);
        }

        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit retire du panier.');

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/update', name: 'cart_update', methods: ['POST'])]
    public function update(Request $request, SessionInterface $session, ProductRepository $productRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?: [];
        $cartKey = (string) ($data['cartKey'] ?? $data['productId'] ?? '');
        $quantity = (int) ($data['quantity'] ?? 1);

        $cart = $session->get('cart', []);

        if ($cartKey === '' || !isset($cart[$cartKey])) {
            return new JsonResponse(['success' => false], Response::HTTP_BAD_REQUEST);
        }

        if ($quantity <= 0) {
            unset($cart[$cartKey]);
        } elseif (($item = $this->normalizeCartItem($cartKey, $cart[$cartKey])) && ($product = $productRepository->find($item['productId'])) && $quantity > (int) $product->getQuantity()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'La quantite demandee depasse le stock disponible.',
            ], Response::HTTP_BAD_REQUEST);
        } elseif (is_array($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $quantity;
        } else {
            $cart[$cartKey] = $quantity;
        }

        $session->set('cart', $cart);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('cart');

        $this->addFlash('success', 'Panier vide.');

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/mini', name: 'cart_mini')]
    public function miniCart(SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cart = $session->get('cart', []);
        $cartData = [];
        $total = 0;
        $itemCount = 0;

        foreach ($cart as $key => $rawItem) {
            $item = $this->normalizeCartItem($key, $rawItem);
            if (!$item) {
                continue;
            }

            $product = $productRepository->find($item['productId']);
            if (!$product) {
                continue;
            }

            $cartData[] = [
                'key' => $item['key'],
                'product' => $product,
                'quantity' => $item['quantity'],
                'size' => $item['size'],
                'color' => $item['color'],
            ];

            $total += $product->getPrice() * $item['quantity'];
            $itemCount += $item['quantity'];
        }

        return $this->render('cart/_mini_cart.html.twig', [
            'cartData' => $cartData,
            'total' => $total,
            'itemCount' => $itemCount,
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

    private function buildCartKey(Product $product, string $size, string $color): string
    {
        return $product->getId() . '_' . substr(sha1($size . '|' . $color), 0, 12);
    }

    private function resolveSelectedSize(Product $product, mixed $selectedSize): ?string
    {
        $selectedSize = trim((string) $selectedSize);

        if ($product->getSizes()->count() === 0) {
            return $selectedSize !== '' ? $selectedSize : 'Taille unique';
        }

        foreach ($product->getSizes() as $size) {
            if (
                strcasecmp((string) $size->getName(), $selectedSize) === 0
                || strcasecmp((string) $size->getCode(), $selectedSize) === 0
            ) {
                return (string) $size->getName();
            }
        }

        return null;
    }

    private function resolveSelectedColor(Product $product, mixed $selectedColor): ?string
    {
        $selectedColor = trim((string) $selectedColor);

        if ($selectedColor === '') {
            return null;
        }

        foreach ($product->getColors() as $availableColor) {
            if (strcasecmp($availableColor, $selectedColor) === 0) {
                return $availableColor;
            }
        }

        return null;
    }

    private function redirectToReferer(Request $request): Response
    {
        $referer = $request->headers->get('referer');

        return $referer ? $this->redirect($referer) : $this->redirectToRoute('cart');
    }
}
