<?php

namespace App\Controller\FrontOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', name: 'app_cart_')]
class CartController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('front_equipement_cart');
    }

    #[Route('/update/{productId}', name: 'update', methods: ['POST'])]
    public function update(int $productId, Request $request): Response
    {
        $quantity = max(0, (int) $request->request->get('quantity', 0));
        $session = $request->getSession();
        $cart = $this->normalizeCart((array) $session->get('cart', []));

        foreach (array_keys($cart) as $key) {
            if (!str_starts_with($key, $productId . '::')) {
                continue;
            }

            if ($quantity <= 0) {
                unset($cart[$key]);
            } else {
                $cart[$key]['quantity'] = $quantity;
            }
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('front_equipement_cart');
    }

    #[Route('/remove/{productId}', name: 'remove', methods: ['POST'])]
    public function remove(int $productId): Response
    {
        return $this->redirectToRoute('front_equipement_remove', ['id' => $productId]);
    }

    #[Route('/clear', name: 'clear', methods: ['POST'])]
    public function clear(Request $request): Response
    {
        $request->getSession()->remove('cart');
        $this->addFlash('success', 'Cart cleared.');

        return $this->redirectToRoute('front_equipement_index');
    }

    /**
     * @param array<mixed> $cart
     * @return array<string, array{id:int,size:string,quantity:int}>
     */
    private function normalizeCart(array $cart): array
    {
        $normalized = [];

        foreach ($cart as $key => $value) {
            if (is_array($value) && isset($value['id'])) {
                $productId = (int) $value['id'];
                $size = strtoupper(trim((string) ($value['size'] ?? 'M')));
                $quantity = max(1, (int) ($value['quantity'] ?? 1));
            } else {
                $productId = (int) $key;
                $size = 'M';
                $quantity = max(1, (int) $value);
            }

            if ($productId <= 0) {
                continue;
            }

            $normKey = $productId . '::' . $size;
            $normalized[$normKey] = [
                'id' => $productId,
                'size' => $size,
                'quantity' => $quantity,
            ];
        }

        return $normalized;
    }
}
