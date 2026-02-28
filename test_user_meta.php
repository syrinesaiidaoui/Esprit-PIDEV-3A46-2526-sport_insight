<?php

use App\Kernel;
use App\Entity\User;

require __DIR__ . '/vendor/autoload.php';

$kernel = new Kernel('dev', true);
$kernel->boot();

$user = new User();
echo "Class: " . get_class($user) . "\n";
echo "Methods: " . implode(', ', get_class_methods($user)) . "\n";

try {
    $container = $kernel->getContainer();
    $entityManager = $container->get('doctrine')->getManager();
    $metadata = $entityManager->getClassMetadata(User::class);

    echo "Identifier field names: " . implode(', ', $metadata->getIdentifierFieldNames()) . "\n";

    $pa = \Symfony\Component\PropertyAccess\PropertyAccess::createPropertyAccessor();
    echo "Can read 'id'? " . ($pa->isReadable($user, 'id') ? 'YES' : 'NO') . "\n";
    echo "ID value: " . $pa->getValue($user, 'id') . "\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "TRACE: " . $e->getTraceAsString() . "\n";
}

$kernel->shutdown();
