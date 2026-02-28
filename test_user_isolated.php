<?php

require __DIR__ . '/src/Entity/User.php';

// Mocking dependencies if needed, or just using reflection
$rc = new ReflectionClass('App\Entity\User');

echo "Class: " . $rc->getName() . "\n";
echo "File: " . $rc->getFileName() . "\n";

foreach ($rc->getMethods() as $method) {
    if (stripos($method->getName(), 'id') !== false) {
        echo "Method: " . $method->getName() . " (Declaring class: " . $method->getDeclaringClass()->getName() . ")\n";
    }
}

foreach ($rc->getProperties() as $prop) {
    if (stripos($prop->getName(), 'id') !== false) {
        echo "Property: " . $prop->getName() . " (Declaring class: " . $prop->getDeclaringClass()->getName() . ")\n";
    }
}
