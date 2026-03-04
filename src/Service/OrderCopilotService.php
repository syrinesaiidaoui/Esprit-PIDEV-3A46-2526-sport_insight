<?php





namespace App\Service;





use App\Entity\ProductOrder\Product;


use App\Entity\User;


use App\Repository\OrderRepository;







/**


 * Lightweight AI-like copilot dedicated to equipment ordering flows.


 * It is rule-based (no external LLM calls) so it stays deterministic and fast.


 */


class OrderCopilotService


{


    private OrderRepository $orders;


    private ProductRepository $products;





    public function __construct(OrderRepository $orders, ProductRepository $products)


    {


        $this->orders = $orders;


        $this->products = $products;


    }





    /**


     * Main entry point used by the chat endpoint.


     * Returns a plain text reply that the front-end formats.


     */


    private bool $isEn = false;





    public function respond(string $userMessage, array $catalog, ?User $user = null, array $sessionOrderIds = [], ?string $locale = null): string


    {


        $this->isEn = $locale ? str_starts_with(strtolower($locale), 'en') : false;


        $normalized = mb_strtolower($userMessage);





        if ($this->isLowSignal($normalized)) {


            return $this->lowSignalReply();


        }





        if ($this->isGreeting($normalized)) {


            return $this->handleGreeting($catalog);


        }





        if ($this->isTranslationRequest($normalized)) {


            return $this->handleTranslation($userMessage);


        }





        if ($this->isHowToOrderRequest($normalized)) {


            return $this->handleHowToOrder();


        }





        if ($this->isStatusRequest($normalized)) {


            return $this->handleStatus($userMessage, $sessionOrderIds);


        }





        if ($this->isChangeRequest($normalized)) {


            return $this->handleChangeRequest($userMessage);


        }





        if ($this->isIssueRequest($normalized)) {


            return $this->handleIssue($userMessage);


        }





        if ($this->isCompatibilityRequest($normalized)) {


            return $this->handleCompatibility($userMessage, $catalog);


        }





        if ($this->isTopProductsRequest($normalized)) {


            return $this->handleTopProducts($catalog);


        }





        if ($this->isCheaperAltRequest($normalized)) {


            return $this->handleCheaperAlternative($userMessage, $catalog);


        }





        // Default: guided ordering intent


        return $this->handleOrderIntent($userMessage, $catalog, $user);


    }





    private function handleOrderIntent(string $message, array $catalog, ?User $user): string


    {


        $parsed = $this->parseOrderIntent($message, $catalog, $user);





        $product = $parsed['product'];


        if ($product === null) {


            return $this->unknownProductReply();


        }





        $productName = $product['name'] ?? 'Article';


        $productId = $parsed['productId'];





        $price = $product['price'] ?? 0.0;
        $qty = $parsed['quantity'] ?? 1;
        $lineTotal = $price * $qty;
        $availability = ($product['stock'] ?? 0) >= $qty
            ? sprintf($this->tr('En stock (%d)', 'In stock (%d)'), (int) ($product['stock'] ?? 0))
            : sprintf($this->tr('Stock bas (%d dispo)', 'Low/Out (%d available)'), (int) ($product['stock'] ?? 0));

        $justification = $this->buildJustification($productName, $qty, $parsed['site'], $parsed['needBy']);
        $approverSummary = $this->buildApproverSummary($productName, $qty, $parsed, $lineTotal);
        $emailDraft = $this->buildSupplierEmail($productName, $qty, $parsed);
        $duplicates = $this->checkDuplicates($productId);
        $missing = $this->listMissingFields($parsed);
        $substitute = $this->suggestSubstitute($product, $catalog);

        $descriptionParts = [
            $this->formatNeedBy($parsed['needBy']) ? $this->tr('Besoin pour : ', 'Need by: ') . $this->formatNeedBy($parsed['needBy']) : null,
            $parsed['site'] ? $this->tr('Site : ', 'Site: ') . $parsed['site'] : null,
            $parsed['budget'] ? $this->tr('Budget : ', 'Budget: ') . $parsed['budget'] : null,
            $parsed['costCenter'] ? $this->tr('Centre de coût : ', 'Cost center: ') . $parsed['costCenter'] : null,
            $parsed['approver'] ? $this->tr('Approbateur : ', 'Approver: ') . $parsed['approver'] : null,
            $this->tr('Justification : ', 'Justification: ') . $justification,
            $this->tr('Synthèse approbateur : ', 'Approver summary: ') . $approverSummary,
            $this->tr('Email fournisseur : ', 'Supplier email: ') . $emailDraft,
        ];



        if ($missing !== '') {


            $descriptionParts[] = 'Infos manquantes : ' . $missing;


        }





        if ($duplicates !== '') {


            $descriptionParts[] = 'Doublon possible : ' . $duplicates;


        }





        if ($substitute !== null) {


            $descriptionParts[] = sprintf(


                'Substitut : %s à %.2f USD (stock %d)',


                $substitute['name'] ?? 'Alternative',


                (float) ($substitute['price'] ?? 0),


                (int) ($substitute['stock'] ?? 0)


            );


        }





        $description = implode(' | ', array_filter($descriptionParts, static fn (?string $part): bool => $part !== null && $part !== ''));





        $lines = [


            'Produit: ' . $productName,


            'Prix: $' . number_format((float) $price, 2, '.', ''),


            'Quantité: ' . $qty,


            'Disponibilité: ' . $availability,


            'Détails: ' . $description,


        ];





        if ($productId > 0) {


            $lines[] = '- Ajouter au panier: /equipement/' . $productId . '/buy';


        }


        $lines[] = '- Voir le panier: /equipement/cart';





        return implode("\n", $lines);


    }





    private function parseOrderIntent(string $message, array $catalog, ?User $user): array


    {


        $quantity = $this->extractQuantity($message);


        $needBy = $this->extractNeedByDate($message);


        $budget = $this->extractBudget($message);


        $site = $this->extractSite($message);


        $costCenter = $this->extractCostCenter($message);


        $approver = $this->extractApprover($message, $user);





        $product = $this->findBestProduct($message, $catalog);


        $productId = $product['id'] ?? 0;





        return [


            'product' => $product,


            'productId' => $productId,


            'quantity' => $quantity,


            'needBy' => $needBy,


            'budget' => $budget,


            'site' => $site,


            'costCenter' => $costCenter,


            'approver' => $approver,


        ];


    }





    private function extractQuantity(string $message): int


    {


        if (preg_match('/\b(\d{1,4})\s?(units?|pcs?|x)?\b/i', $message, $m)) {


            $value = (int) $m[1];


            return $value > 0 ? $value : 1;


        }


        return 1;


    }





    private function extractNeedByDate(string $message): ?\DateTimeImmutable


    {


        $now = new \DateTimeImmutable('now');


        $lower = mb_strtolower($message);





        if (str_contains($lower, 'today')) {


            return $now;


        }


        if (str_contains($lower, 'tomorrow') || str_contains($lower, 'tomorrow') || str_contains($lower, 'tmrw')) {


            return $now->modify('+1 day');


        }


        if (str_contains($lower, 'next week')) {


            return $now->modify('+7 days');


        }





        if (preg_match('/next\s+(monday|tuesday|wednesday|thursday|friday|saturday|sunday)/i', $lower, $m)) {


            return $now->modify('next ' . $m[1]);


        }





        if (preg_match('/(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})/', $message, $m)) {


            return \DateTimeImmutable::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3])) ?: null;


        }





        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})/', $message, $m)) {


            $year = (int) $now->format('Y');


            return \DateTimeImmutable::createFromFormat('Y-m-d', sprintf('%04d-%02d-%02d', $year, $m[2], $m[1])) ?: null;


        }





        return null;


    }





    private function extractBudget(string $message): ?string


    {


        if (preg_match('/(\d+[.,]?\d*)\s?(usd|eur|tnd)?/i', $message, $m)) {


            $amount = str_replace(',', '', $m[1]);


            $currency = strtoupper($m[2] ?? 'USD');


            return $amount . ' ' . $currency;


        }


        return null;


    }





    private function extractSite(string $message): ?string


    {


        $sites = ['sousse', 'tunis', 'sfax', 'paris', 'london', 'new york', 'site', 'warehouse', 'hub'];


        $lower = mb_strtolower($message);


        foreach ($sites as $site) {


            if (str_contains($lower, $site)) {


                return ucwords($site);


            }


        }


        return null;


    }





    private function extractCostCenter(string $message): ?string


    {


        if (preg_match('/cc[:\s-]?([a-z0-9]+)/i', $message, $m)) {


            return 'CC-' . strtoupper($m[1]);


        }


        return null;


    }





    private function extractApprover(string $message, ?User $user): ?string


    {


        if (preg_match('/approv(er|al)[:\s-]+([a-z\s]+)/i', $message, $m)) {


            return ucwords(trim($m[2]));


        }





        if ($user && $user->getUserIdentifier()) {


            return 'Pending approval from ' . $user->getUserIdentifier();


        }





        return null;


    }





    private function findBestProduct(string $message, array $catalog): ?array


    {


        $normalized = $this->normalizeMessage($message);


        foreach ($catalog as $product) {


            $name = mb_strtolower((string)($product['name'] ?? ''));


            if ($name !== '' && str_contains($normalized, $name)) {


                return $product;


            }


        }





        $tokens = preg_split('/[^a-z0-9]+/i', $normalized) ?: [];


        $tokens = array_values(array_filter($tokens, static fn (string $token): bool => strlen($token) >= 3));





        $best = null;


        $bestScore = 0;


        foreach ($catalog as $product) {


            $haystack = mb_strtolower(


                (string)($product['name'] ?? '') . ' '


                . (string)($product['category'] ?? '') . ' '


                . (string)($product['brand'] ?? '')


            );


            $score = 0;


            foreach ($tokens as $token) {


                if (str_contains($haystack, $token)) {


                    $score++;


                }


            }


            if ($score > $bestScore) {


                $bestScore = $score;


                $best = $product;


            }


        }





        if ($bestScore === 0) {


            return null;


        }





        return $best;


    }





    private function normalizeMessage(string $message): string


    {


        $normalized = mb_strtolower($message);





        $replacements = [


            'gants de gardien' => 'gloves',


            'gants gardien' => 'gloves',


            'gants' => 'gloves',


            'gant' => 'gloves',


            'perceuses' => 'drills',


            'perceuse' => 'drill',


            'ballons' => 'balls',


            'ballon' => 'ball',


            'chaussures' => 'boots',


            'chaussure' => 'boots',


            'crampons' => 'boots',


            'maillots' => 'jerseys',


            'maillot' => 'jersey',


            'pantalon' => 'pants',


            'short' => 'short',


        ];





        foreach ($replacements as $from => $to) {


            $normalized = str_replace($from, $to, $normalized);


        }





        return $normalized;


    }





    private function tr(string $fr, string $en): string


    {


        return $this->isEn ? $en : $fr;


    }





    private function buildJustification(string $productName, int $qty, ?string $site, ?\DateTimeImmutable $needBy): string


    {


        $deadline = $this->formatNeedBy($needBy);


        $siteText = $site ? ' for ' . $site : '';





        return sprintf(


            '%d x %s pour éviter une rupture%s. Besoin pour %s.',


            $qty,


            $productName,


            $siteText,


            $deadline ?: 'bientôt'


        );


    }





    private function buildApproverSummary(string $productName, int $qty, array $parsed, float $lineTotal): string


    {


        $eta = $this->formatNeedBy($parsed['needBy']) ?: 'date non précisée';


        $budget = $parsed['budget'] ?: 'non fourni';


        $risk = ($parsed['product']['stock'] ?? 0) < $qty ? 'Risque: stock faible; proposer un split.' : 'Stock OK.';


        $alt = $this->suggestSubstitute($parsed['product'], []) ? 'Substitut moins cher dispo.' : 'Pas de substitut moins cher.';





        return sprintf(


            '%d x %s | Total estimé %.2f USD | Besoin pour %s | Budget %s | %s %s',


            $qty,


            $productName,


            $lineTotal,


            $eta,


            $budget,


            $risk,


            $alt


        );


    }





    private function buildSupplierEmail(string $productName, int $qty, array $parsed): string


    {


        $needBy = $this->formatNeedBy($parsed['needBy']) ?: 'dès que possible';


        $site = $parsed['site'] ?: 'site principal';





        return sprintf(


            'Bonjour, merci de coter %d x %s livré à %s, besoin pour %s. Merci de confirmer le délai, incoterms, paiement et meilleur prix.',


            $qty,


            $productName,


            $site,


            $needBy


        );


    }





    private function listMissingFields(array $parsed): string


    {


        $missing = [];


        if (($parsed['product']['id'] ?? 0) === 0) {


            $missing[] = 'product';


        }


        if (!($parsed['quantity'] ?? null)) {


            $missing[] = 'quantity';


        }


        if (!$parsed['site']) {


            $missing[] = 'site';


        }


        if (!$parsed['needBy']) {


            $missing[] = 'date de besoin';


        }


        if (!$parsed['budget']) {


            $missing[] = 'budget';


        }


        if (!$parsed['approver']) {


            $missing[] = 'approbateur';


        }


        return implode(', ', $missing);


    }





    private function suggestSubstitute(?array $product, array $catalog): ?array


    {


        if ($product === null || empty($catalog)) {


            return null;


        }





        $category = mb_strtolower((string)($product['category'] ?? ''));


        $price = (float) ($product['price'] ?? 0);





        $candidates = array_filter($catalog, static function ($item) use ($category, $price, $product) {


            if (!is_array($item)) {


                return false;


            }


            if ($item === $product) {


                return false;


            }


            $sameCategory = $category !== '' && str_contains(mb_strtolower((string)($item['category'] ?? '')), $category);


            $cheaper = (float) ($item['price'] ?? 0) < ($price * 0.95);


            return $sameCategory && $cheaper;


        });





        if (empty($candidates)) {


            return null;


        }





        usort($candidates, static fn ($a, $b): int => (float)($a['price'] ?? 0) <=> (float)($b['price'] ?? 0));


        return $candidates[0];


    }





    private function checkDuplicates(int $productId): string


    {


        if ($productId <= 0) {


            return '';


        }





        $recent = $this->orders->findBy([], ['orderDate' => 'DESC'], 8);


        $matches = [];


        $since = (new \DateTimeImmutable('now'))->modify('-14 days');





        foreach ($recent as $order) {


            if ($order->getProduct()?->getId() === $productId && $order->getOrderDate() >= $since) {


                $matches[] = sprintf('#%d on %s', $order->getId(), $order->getOrderDate()->format('Y-m-d'));


            }


        }





        if (empty($matches)) {


            return '';


        }





        return 'Similar orders found: ' . implode(', ', $matches);


    }





    private function formatNeedBy(?\DateTimeImmutable $needBy): ?string


    {


        return $needBy ? $needBy->format('Y-m-d') : null;


    }





    private function isStatusRequest(string $normalized): bool


    {


        return str_contains($normalized, 'where is my order')


            || str_contains($normalized, 'order status')


            || str_contains($normalized, 'status of order')


            || str_contains($normalized, 'eta')


            || str_contains($normalized, 'tracking')


            || str_contains($normalized, 'suivre commande')


            || str_contains($normalized, 'statut commande')


            || str_contains($normalized, 'où est ma commande')


            || str_contains($normalized, 'ou est ma commande');


    }





    private function handleStatus(string $message, array $sessionOrderIds): string


    {


        $orderId = $this->extractOrderId($message);


        $orderIds = [];


        if ($orderId !== null) {


            $orderIds[] = $orderId;


        }


        foreach ($sessionOrderIds as $id) {


            if ((int) $id > 0) {


                $orderIds[] = (int) $id;


            }


        }


        $orderIds = array_values(array_unique($orderIds));





        if (empty($orderIds)) {


            return "Please provide your order number (e.g., SI-1234 or database id).";


        }





        $order = $this->orders->find($orderIds[0]);


        if (!$order) {


            return "I couldn't find order " . $orderIds[0] . ". Check the number and try again.";


        }





        $status = $order->getStatus() ?? 'pending';


        $payment = $order->getPaymentStatus() ?? 'pending';


        $eta = $this->estimateEta($order);





        $lines = [


            'Commande : #' . $order->getId(),


            'Statut : ' . $status,


            'Paiement : ' . $payment,


            'ETA : ' . $eta,


            'Prochaine étape : ' . $this->nextStepForStatus($status),


        ];





        return implode("\n", $lines);


    }





    private function isLowSignal(string $normalized): bool


    {


        $stripped = preg_replace('/[^a-z0-9]/i', '', $normalized) ?? '';


        if (strlen($stripped) < 2) {


            return true;


        }





        $tokens = preg_split('/[^a-z0-9]+/i', $normalized) ?: [];


        $tokens = array_values(array_filter($tokens, static fn (string $token): bool => strlen($token) >= 3));





        return empty($tokens);


    }





    private function lowSignalReply(): string


    {


        return "Salut ! Je suis l’assistant boutique Sport Insight. Je peux aider sur les produits, le stock, les commandes ou le paiement. Dites-moi ce que vous cherchez ou demandez « comment commander », « suivre commande #123 » ou « trouve des crampons Adidas ».";


    }





    private function unknownProductReply(): string


    {


        return "Je n’ai pas trouvé ce produit. Donnez-moi le nom ou la catégorie (ex: « trouve des gants », « besoin de 5 cônes pour Sousse »). Je peux aussi suivre vos commandes ou expliquer comment commander.";


    }





    private function extractOrderId(string $message): ?int


    {


        if (preg_match('/#?(\d{1,6})/', $message, $m)) {


            return (int) $m[1];


        }


        return null;


    }





    private function estimateEta($order): string


    {


        $status = $order->getStatus() ?? 'pending';


        return match ($status) {


            'pending' => 'ETA 5-7 jours après confirmation',


            'confirmed' => 'ETA 4-6 jours',


            'shipped' => 'ETA 2-4 jours',


            'delivered' => 'Livrée',


            default => 'ETA non disponible',


        };


    }





    private function nextStepForStatus(string $status): string


    {


        return match ($status) {


            'pending' => 'En attente de confirmation fournisseur; vous pouvez encore changer quantité/site.',


            'confirmed' => 'En attente du délai fournisseur; on peut scinder si urgent.',


            'shipped' => 'Suivi en cours de partage; préparez la réception.',


            'delivered' => 'Commande livrée; clôturez ou signalez un problème.',


            'rejected' => 'Commande refusée; vous pouvez la soumettre à nouveau avec modifications.',


            default => 'Commande en suivi.',


        };


    }





    private function isChangeRequest(string $normalized): bool


    {


        return str_contains($normalized, 'increase qty')


            || str_contains($normalized, 'decrease qty')


            || str_contains($normalized, 'change delivery')


            || str_contains($normalized, 'change site')


            || str_contains($normalized, 'cancel line')


            || str_contains($normalized, 'cancel order')


            || str_contains($normalized, 'augmenter quant')


            || str_contains($normalized, 'diminuer quant')


            || str_contains($normalized, 'modifier site')


            || str_contains($normalized, 'changer site')


            || str_contains($normalized, 'changer livraison')


            || str_contains($normalized, 'annuler commande')


            || str_contains($normalized, 'annuler ligne');


    }





    private function handleChangeRequest(string $message): string


    {


        $orderId = $this->extractOrderId($message);


        $qty = $this->extractQuantity($message);


        $site = $this->extractSite($message);





        $lines = [


            'Demande de modification préparée :',


            $orderId ? 'Commande : #' . $orderId : 'Commande : (non précisée)',


            str_contains(mb_strtolower($message), 'increase') || str_contains(mb_strtolower($message), 'decrease') || str_contains(mb_strtolower($message), 'augmenter') || str_contains(mb_strtolower($message), 'diminuer')


                ? 'Nouvelle quantité : ' . $qty


                : 'Quantité inchangée',


            $site ? 'Nouveau site : ' . $site : 'Site inchangé',


            'Action : générer la demande workflow puis envoyer pour approbation.',


        ];





        return implode("\n", $lines);


    }





    private function isIssueRequest(string $normalized): bool


    {


        return str_contains($normalized, 'wrong item')


            || str_contains($normalized, 'damaged')


            || str_contains($normalized, 'missing')


            || str_contains($normalized, 'issue')


            || str_contains($normalized, 'claim')


            || str_contains($normalized, 'mauvais article')


            || str_contains($normalized, 'retour')


            || str_contains($normalized, 'cass')


            || str_contains($normalized, 'endommag')


            || str_contains($normalized, 'perdu')


            || str_contains($normalized, 'non reçu')


            || str_contains($normalized, 'non livre')


            || str_contains($normalized, 'réclamation');


    }





    private function handleIssue(string $message): string


    {


        $orderId = $this->extractOrderId($message) ?? '(not provided)';





        $steps = [


            'Prise en charge du problème :',


            'Commande : ' . $orderId,


            'À collecter : photos, SKU livré, SKU attendu, quantité impactée.',


            'Réclamation : ticket fournisseur + entrepôt, demande remplacement/avoir.',


            'Suivi : tenir le client informé de l’ETA de résolution.',


        ];





        return implode("\n", $steps);


    }





    private function isCompatibilityRequest(string $normalized): bool


    {


        return str_contains($normalized, 'compatible')


            || str_contains($normalized, 'fit for')


            || str_contains($normalized, 'works with')


            || str_contains($normalized, 'fits');


    }





    private function isTopProductsRequest(string $normalized): bool


    {


        return str_contains($normalized, 'produits phares')


            || str_contains($normalized, 'top produits')


            || str_contains($normalized, 'best sellers')


            || str_contains($normalized, 'meilleures ventes');


    }





    private function handleTopProducts(array $catalog): string


    {


        if (empty($catalog)) {


            return 'Catalogue indisponible pour le moment.';


        }





        $top = array_slice($catalog, 0, 3);


        $lines = ['Produits phares du moment :'];


        foreach ($top as $item) {


            $lines[] = sprintf(


                '- %s — %.2f USD — stock %d',


                $item['name'] ?? 'Article',


                (float)($item['price'] ?? 0),


                (int)($item['stock'] ?? 0)


            );


        }


        $lines[] = 'Dites « trouve <nom> » ou « ajoute <nom> » pour aller plus loin.';


        return implode("\n", $lines);


    }





    private function isCheaperAltRequest(string $normalized): bool


    {


        return str_contains($normalized, 'équivalent moins cher')


            || str_contains($normalized, 'alternative moins cher')


            || str_contains($normalized, 'substitut moins cher')


            || str_contains($normalized, 'cheaper substitute')


            || str_contains($normalized, 'cheaper alternative');


    }





    private function handleCheaperAlternative(string $message, array $catalog): string


    {


        $product = $this->findBestProduct($message, $catalog);


        if ($product === null) {


            return "Pour proposer un équivalent moins cher, j'ai besoin du nom de l'article (ex: « équivalent moins cher aux gants Pro »).";


        }





        $alt = $this->suggestSubstitute($product, $catalog);


        if ($alt === null) {


            return "Pas de substitut moins cher détecté pour cet article. Je peux chercher un autre type si vous précisez.";


        }





        return sprintf(


            'Substitut proposé : %s à %.2f USD (stock %d).',


            $alt['name'] ?? 'Alternative',


            (float)($alt['price'] ?? 0),


            (int)($alt['stock'] ?? 0)


        );


    }





    private function isGreeting(string $normalized): bool


    {


        return in_array($normalized, ['hi', 'hello', 'hey', 'salut', 'bonjour', 'hola'], true)


            || preg_match('/^(hi|hello|hey|salut|bonjour|hola)[!. ]*$/i', $normalized) === 1;


    }





    private function handleGreeting(array $catalog): string


    {


        $top = array_slice($catalog, 0, 3);


        $topNames = array_map(static fn ($p) => $p['name'] ?? 'Product', $top);


        $lines = [


            'Bonjour ! Bienvenue sur la boutique Sport Insight.',


            'Je peux vous aider à commander du matériel, vérifier le stock, suivre vos commandes ou rédiger un email fournisseur.',


            'Options rapides :',


            '- Produits phares : /equipement',


            '- Chercher un article : dites "trouve <produit>"',


            '- Comment commander : ajoutez au panier puis allez sur /equipement/cart',


            '- Mes commandes : /equipement/orders',


        ];





        if (!empty($topNames)) {


            $lines[] = 'Featured now: ' . implode(' | ', array_slice($topNames, 0, 3));


        }





        return implode("\n", $lines);


    }





    private function handleCompatibility(string $message, array $catalog): string


    {


        $product = $this->findBestProduct($message, $catalog);


        $name = $product['name'] ?? 'equipment';


        $fits = str_contains(mb_strtolower($message), 'not') ? 'Non' : 'Oui';


        $alts = $this->suggestSubstitute($product, $catalog);





        $lines = [


            'Produit: ' . $name,


            'Compatibilité: ' . $fits . ' (selon le catalogue).',


        ];





        if ($alts) {


            $lines[] = 'Alternative: ' . ($alts['name'] ?? 'alternative') . ' à ' . number_format((float)($alts['price'] ?? 0), 2) . ' USD.';


        }





        return implode("\n", $lines);


    }





    private function isHowToOrderRequest(string $normalized): bool


    {


        return str_contains($normalized, 'comment commander')


            || str_contains($normalized, 'how to order')


            || str_contains($normalized, 'passer commande')


            || str_contains($normalized, 'procédure de commande')


            || str_contains($normalized, 'help order');


    }





    private function handleHowToOrder(): string


    {


        return implode("\n", [


            'Pour commander sur Sport Insight :',


            '1) Trouvez un produit et cliquez « Ajouter au panier ».',


            '2) Ouvrez le panier pour vérifier quantités et tailles.',


            '3) Passez au paiement puis remplissez contact, adresse, paiement.',


            '4) Suivez vos commandes dans la section commandes.',


            'Je peux aussi remplir le panier si vous dites « Ajoute 2 gants taille M ».',


        ]);


    }





    private function isTranslationRequest(string $normalized): bool


    {


        return str_starts_with($normalized, 'translate')


            || str_contains($normalized, 'translate to')


            || str_contains($normalized, 'traduire')


            || str_contains($normalized, '????');


    }





    private function handleTranslation(string $message): string


    {


        if (preg_match('/translate (?:to )?(french|fr|arabic|ar|english|en)[: ]+(.*)/i', $message, $m)) {


            $lang = strtolower($m[1]);


            $text = trim($m[2]);


        } else {


            // Fallback: everything after the keyword


            $text = trim(preg_replace('/^translate/i', '', $message));


            $lang = 'en';


        }





        $translated = $this->fakeTranslate($text, $lang);





        return 'Translation (' . strtoupper($lang) . '): ' . $translated;


    }





    /**


     * Simple deterministic "translation" to avoid external calls.


     * It keeps the same text and tags it with the target language for now.


     */


    private function fakeTranslate(string $text, string $lang): string


    {


        return '[' . strtoupper($lang) . '] ' . $text;


    }


}















