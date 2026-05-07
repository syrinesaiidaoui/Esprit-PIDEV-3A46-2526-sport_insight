<?php

namespace App\Controller\FrontOffice;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/checkout', name: 'app_checkout_')]
class CheckoutController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET', 'POST'])]
    public function index(Request $request, CartService $cartService): Response
    {
        $session = $request->getSession();
        $unifiedCart = $this->normalizeUnifiedCart((array) $session->get('cart', []));
        $legacyCart = $cartService->getCart();

        $beforeCount = count($unifiedCart);
        $unifiedCart = $this->mergeLegacyCartIntoUnifiedCart($unifiedCart, $legacyCart);
        if (count($unifiedCart) !== $beforeCount) {
            $session->set('cart', $unifiedCart);
        }

        if (!empty($legacyCart)) {
            $cartService->clearCart();
        }

        if (empty($unifiedCart)) {
            $this->addFlash('warning', 'Your cart is empty.');
            return $this->redirectToRoute('front_equipement_index');
        }

        return $this->redirectToRoute('front_equipement_checkout');
    }

    /**
     * @param array<mixed> $cart
     * @return array<string, array{id:int,size:string,quantity:int}>
     */
    private function normalizeUnifiedCart(array $cart): array
    {
        $normalized = [];

        foreach ($cart as $key => $line) {
            if (!is_array($line) || !isset($line['id'])) {
                continue;
            }

            $productId = (int) $line['id'];
            if ($productId <= 0) {
                continue;
            }

            $size = strtoupper(trim((string) ($line['size'] ?? 'M')));
            $quantity = max(1, (int) ($line['quantity'] ?? 1));
            $normKey = $productId . '::' . $size;

            if (!isset($normalized[$normKey])) {
                $normalized[$normKey] = [
                    'id' => $productId,
                    'size' => $size,
                    'quantity' => 0,
                ];
            }

            $normalized[$normKey]['quantity'] += $quantity;
        }

        return $normalized;
    }

    /**
     * @param array<string, array{id:int,size:string,quantity:int}> $unifiedCart
     * @param array<mixed> $legacyCart
     * @return array<string, array{id:int,size:string,quantity:int}>
     */
    private function mergeLegacyCartIntoUnifiedCart(array $unifiedCart, array $legacyCart): array
    {
        foreach ($legacyCart as $legacyLine) {
            if (!is_array($legacyLine)) {
                continue;
            }

            $product = $legacyLine['product'] ?? null;
            if (!is_object($product) || !method_exists($product, 'getId') || !method_exists($product, 'getSize')) {
                continue;
            }

            $productId = (int) $product->getId();
            if ($productId <= 0) {
                continue;
            }

            $size = strtoupper(trim((string) ($product->getSize() ?: 'M')));
            $quantity = max(1, (int) ($legacyLine['quantity'] ?? 1));
            $key = $productId . '::' . $size;

            if (!isset($unifiedCart[$key])) {
                $unifiedCart[$key] = [
                    'id' => $productId,
                    'size' => $size,
                    'quantity' => 0,
                ];
            }

            $unifiedCart[$key]['quantity'] += $quantity;
        }

        return $unifiedCart;
    }
}
