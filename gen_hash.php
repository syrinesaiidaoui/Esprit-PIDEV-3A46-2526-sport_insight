<?php
$pwd='coach123!';
$h=password_hash($pwd, PASSWORD_BCRYPT);
echo $h;
