<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    // In development mode, allow long-running requests (e.g., local debugging,
    // heavy config parsing, or asset importmap processing). This is guarded so
    // it doesn't affect production behavior.
    if (PHP_SAPI !== 'cli' && !empty($context['APP_DEBUG']) && function_exists('set_time_limit')) {
        @set_time_limit(0);
    }

    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
