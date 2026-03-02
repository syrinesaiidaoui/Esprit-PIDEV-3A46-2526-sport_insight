<?php

namespace App\Controller\FrontOffice;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\ProductOrder\Order;
use App\Entity\ProductOrder\Product;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Service\OrderNotificationService;
use App\Service\OrderCopilotService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[Route('/equipement')]
class EquipementController extends AbstractController
{
    // =========================
    //  Product listing
    // =========================
    #[Route('/', name: 'front_equipement_index')]
    public function index(ProductRepository $repo, Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $this->syncJsonCatalogToDatabase($repo, $em);

        $q = $request->query->get('q');
        $category = $request->query->get('category');
        $sort = $request->query->get('sortBy');
        $dir = strtolower((string) $request->query->get('dir', 'asc')) === 'desc' ? 'DESC' : 'ASC';
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 9;

        try {
            $productsQuery = $repo->createSearchQueryBuilder($q, $category, $sort, $dir);
            $pagination = $paginator->paginate($productsQuery, $page, $perPage);
            $products = $pagination->getItems();
        } catch (\Throwable $e) {
            $productsQuery = $repo->createSearchQueryBuilder($q, $category, null, 'ASC');
            $pagination = $paginator->paginate($productsQuery, $page, $perPage);
            $products = $pagination->getItems();
        }
        $categories = $repo->findDistinctCategories();

        $apiProducts = [];
        $apiFilePath = $this->getParameter('kernel.project_dir') . '/public/api/products.json';
        if (is_file($apiFilePath)) {
            $apiRaw = file_get_contents($apiFilePath);
            $apiDecoded = json_decode($apiRaw ?: '[]', true);
            if (is_array($apiDecoded)) {
                $apiProducts = $apiDecoded;
                foreach ($apiProducts as &$apiProduct) {
                    if (!is_array($apiProduct)) {
                        continue;
                    }
                    $name = trim((string) ($apiProduct['name'] ?? ''));
                    if ($name === '') {
                        continue;
                    }
                    $dbProduct = $repo->findOneBy(['name' => $name]);
                    if ($dbProduct) {
                        $apiProduct['dbId'] = $dbProduct->getId();
                    }
                }
                unset($apiProduct);
            }
        }

        return $this->render('front_office/equipement/index.html.twig', [
            'products' => $products,
            'page' => $pagination->getCurrentPageNumber(),
            'totalPages' => $pagination->getPageCount(),
            'totalProducts' => $pagination->getTotalItemCount(),
            'categories' => $categories,
            'apiProducts' => $apiProducts,
            'apiProductsUrl' => $this->generateUrl('api_catalog_products'),
        ]);
    }

    private function syncJsonCatalogToDatabase(ProductRepository $repo, EntityManagerInterface $em): void
    {
        $apiFilePath = $this->getParameter('kernel.project_dir') . '/public/api/products.json';
        if (!is_file($apiFilePath)) {
            return;
        }

        $apiRaw = file_get_contents($apiFilePath);
        $apiDecoded = json_decode($apiRaw ?: '[]', true);
        if (!is_array($apiDecoded)) {
            return;
        }

        $hasChanges = false;
        foreach ($apiDecoded as $item) {
            if (!is_array($item)) {
                continue;
            }

            $name = trim((string) ($item['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $product = $repo->findOneBy(['name' => $name]);
            if (!$product) {
                $product = new Product();
                $product->setName($name);
                $em->persist($product);
            }

            $product->setCategory((string) ($item['category'] ?? 'Football'));
            $product->setPrice((string) ((float) ($item['price'] ?? 0)));
            $product->setStock((int) ($item['stock'] ?? 0));
            $product->setSize((string) ($item['size'] ?? 'M'));
            $product->setBrand((string) ($item['brand'] ?? 'Generique'));
            $image = trim((string) ($item['image'] ?? ''));
            if ($image !== '' && !str_starts_with($image, 'http://') && !str_starts_with($image, 'https://') && !str_starts_with($image, 'api/') && !str_starts_with($image, '/api/')) {
                $image = 'api/' . ltrim($image, '/');
            }
            $product->setImage($image !== '' ? $image : null);

            $hasChanges = true;
        }

        if ($hasChanges) {
            $em->flush();
        }
    }

    // =========================
    //  Buy product (cart)
    // =========================
    #[Route('/{id}/buy', name: 'front_equipement_buy', requirements: ['id' => '\d+'])]
    public function buy(Product $product, EntityManagerInterface $em, Request $request): Response
    {
        $session = $request->getSession();
        $cart = $this->normalizeCart($session->get('cart', []));
        $id = $product->getId();

        $allowedSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '5', '6', '7', '8', '9', '10'];
        $size = strtoupper(trim((string) $request->get('size', $product->getSize() ?: 'M')));
        if (!in_array($size, $allowedSizes, true)) {
            $this->addFlash('danger', 'Merci de choisir une taille valide.');
            return $this->redirectToRoute('front_equipement_show', ['id' => $id]);
        }

        $key = $id . '::' . $size;
        $cart[$key] = [
            'id' => $id,
            'size' => $size,
            'quantity' => ($cart[$key]['quantity'] ?? 0) + 1,
        ];

        $session->set('cart', $cart);
        $this->addFlash('success', sprintf('Ajoute au panier (taille %s).', $size));
        return $this->redirectToRoute('front_equipement_index');
    }

    // =========================
    //  Show product details
    // =========================
    #[Route('/{id}', name: 'front_equipement_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Product $product): Response
    {
        return $this->render('front_office/equipement/show.html.twig', [
            'product' => $product,
        ]);
    }

    // =========================
    //  Remove from cart
    // =========================
    #[Route('/{id}/remove', name: 'front_equipement_remove', requirements: ['id' => '\d+'])]
    public function remove(Product $product, Request $request): Response
    {
        $session = $request->getSession();
        $cart = $this->normalizeCart($session->get('cart', []));
        $id = $product->getId();
        $size = strtoupper(trim((string) $request->query->get('size', '')));

        if ($size !== '') {
            $key = $id . '::' . $size;
            unset($cart[$key]);
        } else {
            foreach (array_keys($cart) as $key) {
                if (str_starts_with($key, $id . '::')) {
                    unset($cart[$key]);
                }
            }
        }

        $session->set('cart', $cart);
        $this->addFlash('success', 'Produit retire du panier.');
        return $this->redirectToRoute('front_equipement_cart');
    }

    // =========================
    //  Cart page
    // =========================
    #[Route('/cart', name: 'front_equipement_cart')]
    public function cart(ProductRepository $repo, Request $request): Response
    {
        $session = $request->getSession();
        $cart = $this->normalizeCart($session->get('cart', []));
        $items = [];
        $total = 0;

        foreach ($cart as $line) {
            $product = $repo->find($line['id'] ?? 0);
            if ($product) {
                $qty = (int) ($line['quantity'] ?? 1);
                $items[] = [
                    'product' => $product,
                    'quantity' => $qty,
                    'size' => $line['size'] ?? $product->getSize(),
                ];
                $total += floatval($product->getPrice()) * $qty;
            }
        }

        return $this->render('front_office/equipement/cart.html.twig', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    // =========================
    //  Checkout
    // =========================
    #[Route('/checkout', name: 'front_equipement_checkout', methods: ['GET', 'POST'])]
    public function checkout(
        Request $request,
        EntityManagerInterface $em,
        ProductRepository $repo,
        CsrfTokenManagerInterface $csrfManager,
        OrderNotificationService $orderNotificationService,
        #[Autowire(service: 'state_machine.order_status')]
        WorkflowInterface $orderWorkflow
    ): Response {
        $session = $request->getSession();
        $cart = $this->normalizeCart($session->get('cart', []));
        if (empty($cart)) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('front_equipement_index');
        }

        $items = [];
        $total = 0.0;
        foreach ($cart as $line) {
            $product = $repo->find($line['id'] ?? 0);
            if (!$product) {
                continue;
            }
            $qty = (int) ($line['quantity'] ?? 1);
            $size = $line['size'] ?? $product->getSize();
            $lineTotal = floatval($product->getPrice()) * $qty;
            $items[] = ['product' => $product, 'quantity' => $qty, 'size' => $size, 'lineTotal' => $lineTotal];
            $total += $lineTotal;
        }

        if ($request->isMethod('GET')) {
            return $this->render('front_office/equipement/checkout.html.twig', [
                'items' => $items,
                'total' => $total,
            ]);
        }

        $token = new CsrfToken('checkout', $request->request->get('_token'));
        if (!$csrfManager->isTokenValid($token)) {
            $this->addFlash('danger', 'Jeton CSRF invalide.');
            return $this->redirectToRoute('front_equipement_checkout');
        }

        $fullName = trim((string) $request->request->get('full_name', ''));
        $emailInput = trim((string) $request->request->get('email', ''));
        $phone = trim((string) $request->request->get('phone', ''));
        $address = trim((string) $request->request->get('address', ''));
        $city = trim((string) $request->request->get('city', ''));
        $postalCode = trim((string) $request->request->get('postal_code', ''));
        $paymentMethod = (string) $request->request->get('payment_method', 'cod');
        $cardHolder = trim((string) $request->request->get('card_holder', ''));
        $cardNumber = preg_replace('/\s+/', '', (string) $request->request->get('card_number', ''));
        $cardExpiry = trim((string) $request->request->get('card_expiry', ''));
        $cardCvv = trim((string) $request->request->get('card_cvv', ''));

        if ($fullName === '' || $emailInput === '' || $phone === '' || $address === '' || $city === '' || $postalCode === '') {
            $this->addFlash('danger', 'Merci de renseigner toutes les informations client.');
            return $this->redirectToRoute('front_equipement_checkout');
        }

        if (!filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'Adresse email invalide.');
            return $this->redirectToRoute('front_equipement_checkout');
        }
        if (!in_array($paymentMethod, ['cod', 'online'], true)) {
            $this->addFlash('danger', 'Mode de paiement invalide.');
            return $this->redirectToRoute('front_equipement_checkout');
        }
        if ($paymentMethod === 'online') {
            if ($cardHolder === '' || strlen($cardHolder) < 3) {
                $this->addFlash('danger', 'Nom du porteur de carte invalide.');
                return $this->redirectToRoute('front_equipement_checkout');
            }
            if (!preg_match('/^\d{16}$/', (string) $cardNumber)) {
                $this->addFlash('danger', 'Numero de carte invalide (16 chiffres).');
                return $this->redirectToRoute('front_equipement_checkout');
            }
            if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $cardExpiry)) {
                $this->addFlash('danger', 'Date d\'expiration invalide (MM/YY).');
                return $this->redirectToRoute('front_equipement_checkout');
            }
            if (!preg_match('/^\d{3,4}$/', $cardCvv)) {
                $this->addFlash('danger', 'CVV invalide.');
                return $this->redirectToRoute('front_equipement_checkout');
            }
        }

        $user = $this->getUser();
        $orders = [];
        $invoiceLines = [];
        foreach ($cart as $line) {
            $product = $repo->find($line['id'] ?? 0);
            if (!$product) {
                continue;
            }
            $qty = (int) ($line['quantity'] ?? 1);
            $size = $line['size'] ?? $product->getSize();
            if ($product->getStock() < $qty) {
                $this->addFlash('danger', sprintf('Stock insuffisant pour %s', $product->getName()));
                return $this->redirectToRoute('front_equipement_cart');
            }

            $order = new Order();
            $order->setProduct($product);
            $order->setQuantity($qty);
            $order->setSize($size);
            $order->setOrderDate(new \DateTime());
            $shippingAddress = trim($address . ', ' . $city . ' ' . $postalCode);
            $order->setContactEmail($emailInput);
            $order->setContactPhone($phone);
            $order->setShippingAddress($shippingAddress);
            $order->setBillingAddress($shippingAddress);
            $order->setPaymentMethod($paymentMethod);
            $order->setTotalAmount(number_format((float) $product->getPrice() * (int) $qty, 2, '.', ''));

            if ($paymentMethod === 'online') {
                $order->setPaymentStatus('paid');
                $order->setStatus('pending');
                if ($orderWorkflow->can($order, 'pay')) {
                    $orderWorkflow->apply($order, 'pay');
                }
            } else {
                $order->setPaymentStatus('pending');
                $order->setStatus('pending');
            }
            if ($user) {
                $order->setEntraineur($user);
            }

            $product->setStock($product->getStock() - $qty);
            $em->persist($order);
            $em->persist($product);
            $orders[] = $order;

            $invoiceLines[] = sprintf(
                "%s | Taille: %s | Quantite: %d | PU: %.2f USD | Total: %.2f USD",
                (string) $product->getName(),
                (string) $size,
                (int) $qty,
                (float) $product->getPrice(),
                (float) $product->getPrice() * (int) $qty
            );
        }

        $em->flush();

        $invoiceNumber = 'SI-' . date('Ymd-His');
        $invoiceText = $this->buildInvoiceText(
            $invoiceNumber,
            $fullName,
            $emailInput,
            $phone,
            $address,
            $city,
            $postalCode,
            $invoiceLines,
            $total
        );

        $session->set('invoice_text', $invoiceText);
        $session->set('invoice_filename', sprintf('facture-%s.txt', strtolower($invoiceNumber)));

        try {
            $orderNotificationService->sendOrderConfirmation($emailInput, $fullName, $orders);
            if ($paymentMethod === 'online') {
                $orderNotificationService->sendPaymentConfirmation($emailInput, $fullName, $orders);
            }
        } catch (\Throwable) {
            $this->addFlash('warning', "Commande validee, mais certains emails n'ont pas pu etre envoyes.");
        }

        if (!empty($orders)) {
            $existingOrderIds = $session->get('customer_order_ids', []);
            if (!is_array($existingOrderIds)) {
                $existingOrderIds = [];
            }
            $newOrderIds = array_map(static fn(Order $order): int => (int) $order->getId(), $orders);
            $customerOrderIds = array_values(array_unique(array_filter(array_merge($existingOrderIds, $newOrderIds), static fn(mixed $id): bool => (int) $id > 0)));
            $session->set('customer_order_ids', $customerOrderIds);
        }

        $session->remove('cart');
        return $this->redirectToRoute('front_equipement_checkout_success');
    }

    #[Route('/checkout-success', name: 'front_equipement_checkout_success')]
    public function checkoutSuccess(): Response
    {
        return $this->render('front_office/equipement/checkout_success.html.twig');
    }

    private function normalizeCart(array $cart): array
    {
        $normalized = [];
        foreach ($cart as $key => $value) {
            if (is_array($value) && isset($value['id'])) {
                $pid = (int) $value['id'];
                $size = strtoupper(trim((string) ($value['size'] ?? 'M')));
                $qty = max(1, (int) ($value['quantity'] ?? 1));
            } else {
                $pid = (int) $key;
                $size = 'M';
                $qty = max(1, (int) $value);
            }
            $normKey = $pid . '::' . $size;
            if (!isset($normalized[$normKey])) {
                $normalized[$normKey] = ['id' => $pid, 'size' => $size, 'quantity' => 0];
            }
            $normalized[$normKey]['quantity'] += $qty;
        }
        return $normalized;
    }

    #[Route('/invoice/download', name: 'front_equipement_invoice_download', methods: ['GET'])]
    public function downloadInvoice(Request $request): Response
    {
        $session = $request->getSession();
        $invoiceText = (string) $session->get('invoice_text', '');
        $filename = (string) $session->get('invoice_filename', 'facture-sport-insight.txt');

        if ($invoiceText === '') {
            $this->addFlash('warning', 'Aucune facture disponible au telechargement.');
            return $this->redirectToRoute('front_equipement_orders');
        }

        $response = new Response($invoiceText);
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'text/plain; charset=UTF-8');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    #[Route('/orders', name: 'front_equipement_orders')]
    public function orders(Request $request, OrderRepository $orderRepo): Response
    {
        $user = $this->getUser();
        if ($user) {
            $orders = $orderRepo->findBy(['entraineur' => $user], ['orderDate' => 'DESC']);
        } else {
            $sessionOrderIds = $request->getSession()->get('customer_order_ids', []);
            if (!is_array($sessionOrderIds) || empty($sessionOrderIds)) {
                $orders = [];
            } else {
                $orders = $orderRepo->findBy(['id' => array_values(array_unique($sessionOrderIds))]);
                usort($orders, static fn(Order $a, Order $b): int => $b->getOrderDate() <=> $a->getOrderDate());
            }
        }

        return $this->render('front_office/equipement/orders.html.twig', [
            'orders' => $orders,
        ]);
    }

    // =========================
    //  AI Chat endpoint
    // =========================
    #[Route('/ai-chat', name: 'front_equipement_ai_chat', methods: ['POST'])]
    public function aiChat(Request $request, ProductRepository $productRepo, OrderCopilotService $copilotService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = trim((string) ($data['message'] ?? ''));
        if ($userMessage === '') {
            return new JsonResponse(['reply' => 'Veuillez saisir un message.'], 400);
        }

        $catalog = $this->buildStoreCatalog($productRepo);
        if (empty($catalog)) {
            return new JsonResponse(['reply' => 'Je ne peux pas acceder au catalogue pour le moment. Veuillez reessayer dans un instant.'], 500);
        }

        $sessionOrders = $request->getSession()->get('customer_order_ids', []);
        if (!is_array($sessionOrders)) {
            $sessionOrders = [];
        }

        return new JsonResponse([
            'reply' => $copilotService->respond($userMessage, $catalog, $this->getUser(), $sessionOrders, $request->getLocale())
        ]);
    }

    private function buildStoreCatalog(ProductRepository $productRepo): array
    {
        $catalog = [];
        $knownNames = [];

        $products = array_slice($productRepo->findAll(), 0, 80);
        foreach ($products as $product) {
            $name = (string) $product->getName();
            if ($name === '') {
                continue;
            }
            $nameKey = mb_strtolower($name);
            $knownNames[$nameKey] = true;

            $catalog[] = [
                'id' => $product->getId(),
                'name' => $name,
                'category' => $product->getCategory() ?: 'N/A',
                'price' => (float) $product->getPrice(),
                'stock' => (int) $product->getStock(),
                'size' => $product->getSize() ?: 'N/A',
                'brand' => $product->getBrand() ?: 'N/A',
                'description' => sprintf('%s product by %s, size %s.', $product->getCategory() ?: 'Shop', $product->getBrand() ?: 'Generic', $product->getSize() ?: 'N/A'),
                'slug' => $this->slugify($name),
                'imageUrl' => $this->buildImageUrl((string) $product->getImage()),
            ];
        }

        $apiFilePath = $this->getParameter('kernel.project_dir') . '/public/api/products.json';
        if (is_file($apiFilePath)) {
            $apiRaw = file_get_contents($apiFilePath);
            $apiDecoded = json_decode($apiRaw ?: '[]', true);
            if (is_array($apiDecoded)) {
                foreach ($apiDecoded as $item) {
                    if (!is_array($item))
                        continue;
                    $name = trim((string) ($item['name'] ?? ''));
                    if ($name === '')
                        continue;
                    $nameKey = mb_strtolower($name);
                    if (isset($knownNames[$nameKey]))
                        continue;
                    $knownNames[$nameKey] = true;

                    $catalog[] = [
                        'id' => isset($item['id']) ? (int) $item['id'] : (count($catalog) + 1),
                        'name' => $name,
                        'category' => (string) ($item['category'] ?? 'N/A'),
                        'price' => (float) ($item['price'] ?? 0),
                        'stock' => (int) ($item['stock'] ?? 0),
                        'size' => (string) ($item['size'] ?? 'N/A'),
                        'brand' => (string) ($item['brand'] ?? 'N/A'),
                        'description' => (string) ($item['description'] ?? 'Aucune description'),
                        'slug' => $this->slugify($name),
                        'imageUrl' => $this->buildImageUrl((string) ($item['image'] ?? '')),
                    ];
                }
            }
        }
        return $catalog;
    }

    private function buildInvoiceText(string $invoiceNumber, string $fullName, string $email, string $phone, string $address, string $city, string $postalCode, array $invoiceLines, float $total): string
    {
        $header = [
            'SPORT INSIGHT - FACTURE',
            'Numero: ' . $invoiceNumber,
            'Date: ' . date('Y-m-d H:i:s'),
            '',
            'Informations client',
            'Nom: ' . $fullName,
            'Email: ' . $email,
            'Telephone: ' . $phone,
            'Adresse: ' . $address . ', ' . $city . ' ' . $postalCode,
            '',
            'Produits commandes',
        ];
        $footer = ['', sprintf('TOTAL: %.2f USD', $total), '', 'Merci pour votre commande Sport Insight.'];
        return implode("\n", array_merge($header, $invoiceLines, $footer));
    }

    private function buildImageUrl(string $image): string
    {
        $value = trim($image);
        if ($value === '')
            return '';
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://'))
            return $value;
        if (str_starts_with($value, '/'))
            return $value;
        if (str_starts_with($value, 'api/'))
            return '/' . $value;
        return '/uploads/' . ltrim($value, '/');
    }

    private function slugify(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';
        $value = trim($value, '-');
        return $value !== '' ? $value : 'product';
    }

    #[Route('/{id}', name: 'app_front_equipement_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }
        return $this->redirectToRoute('front_equipement_index');
    }
}
