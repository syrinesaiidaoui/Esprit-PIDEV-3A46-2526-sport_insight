<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/sport_insightt/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/sport_insightt/.env');

$kernel = new App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$container = $kernel->getContainer();
$em = $container->get('doctrine')->getManager();
$contracts = $em->getRepository(App\Entity\ContratSponsor::class)->findAll();

$count = 0;
foreach($contracts as $c) {
    if($c->isExpired()) {
        $c->setNotified(false);
        if($c->getEquipe()) {
            $c->getEquipe()->setTelephone('93677092');
        }
        $count++;
    }
}
$em->flush();
echo "Updated $count expired contracts.\n";
