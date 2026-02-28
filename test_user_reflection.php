<?php

use App\Kernel;
use App\Entity\User;

require __DIR__ . '/vendor/autoload.php';

$kernel = new Kernel('dev', true);
$kernel->boot();

$user = new User();
$rc = new ReflectionClass(User::class);

echo "Class: " . $rc->getName() . "\n";
foreach ($rc->getMethods() as $method) {
    if (stripos($method->getName(), 'id') !== false) {
        echo "Method: " . $method->getName() . "\n";
    }
}

foreach ($rc->getProperties() as $prop) {
    if (stripos($prop->getName(), 'id') !== false) {
        echo "Property: " . $prop->getName() . "\n";
    }
}

$kernel->shutdown();
