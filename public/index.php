<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
<<<<<<< HEAD
=======
    // In development mode, allow long-running requests (e.g., local debugging,
    // heavy config parsing, or asset importmap processing). This is guarded so
    // it doesn't affect production behavior.
    if (PHP_SAPI !== 'cli' && !empty($context['APP_DEBUG']) && function_exists('set_time_limit')) {
        @set_time_limit(0);
    }

>>>>>>> a3faf68b6604ba7c00e7a1f70865a40a96aacf2d
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
