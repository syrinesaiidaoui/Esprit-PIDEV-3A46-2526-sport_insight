<?php

namespace App\Controller\FrontOffice;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shop', name: 'app_shop_')]
class ShopController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $q = trim((string) $request->query->get('search', ''));
        $category = trim((string) $request->query->get('category', ''));
        $sort = trim((string) $request->query->get('sort', ''));
        $direction = trim((string) $request->query->get('order', ''));

        $params = [];
        if ($q !== '') {
            $params['q'] = $q;
        }
        if ($category !== '') {
            $params['category'] = $category;
        }
        if (in_array($sort, ['name', 'price', 'stock'], true)) {
            $params['sortBy'] = $sort;
        }
        if (in_array(strtolower($direction), ['asc', 'desc'], true)) {
            $params['dir'] = strtolower($direction);
        }

        return $this->redirectToRoute('front_equipement_index', $params);
    }

    #[Route('/product/{id}', name: 'product_detail', methods: ['GET'])]
    public function productDetail(int $id): Response
    {
        return $this->redirectToRoute('front_equipement_show', ['id' => $id]);
    }

    #[Route('/add-to-cart/{id}', name: 'add_to_cart', methods: ['POST'])]
    public function addToCart(int $id, Request $request, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            $this->addFlash('error', 'Product not found.');
            return $this->redirectToRoute('front_equipement_index');
        }

        $quantity = max(1, (int) $request->request->get('quantity', 1));
        $size = strtoupper(trim((string) ($request->request->get('size', $product->getSize() ?: 'M'))));
        $key = $id . '::' . $size;

        $session = $request->getSession();
        $cart = (array) $session->get('cart', []);
        $existingQuantity = (int) ($cart[$key]['quantity'] ?? 0);
        $newQuantity = min($existingQuantity + $quantity, (int) $product->getStock());

        $cart[$key] = [
            'id' => $id,
            'size' => $size,
            'quantity' => max(1, $newQuantity),
        ];
        $session->set('cart', $cart);

        $this->addFlash('success', sprintf('%d x %s added to cart.', $quantity, (string) $product->getName()));

        return $this->redirectToRoute('front_equipement_cart');
    }
}
