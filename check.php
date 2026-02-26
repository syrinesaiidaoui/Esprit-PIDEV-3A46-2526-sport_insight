<?php
$pdo = new PDO('sqlite:var/data_dev.db');
$hash = $pdo->query("select password from user where email='tester@example.com'")->fetchColumn();
var_dump(password_verify('Test1234!', $hash));
$hash2 = $pdo->query("select password from user where email='admin@example.com'")->fetchColumn();
var_dump(password_verify('admin123', $hash2));
