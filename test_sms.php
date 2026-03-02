<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/sport_insightt/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/sport_insightt/.env');

$kernel = new App\Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$container = $kernel->getContainer();
$twilio = $container->get(App\Service\TwilioService::class);

$phone = '93677092';
echo "Sending SMS to $phone...\n";
$success = $twilio->sendSms($phone, "Test SMS from Sport Insight!");
if ($success) {
    echo "SMS Sent successfully!\n";
} else {
    echo "Failed to send SMS.\n";
}
