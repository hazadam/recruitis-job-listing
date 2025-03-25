<?php

declare(strict_types=1);

use App\Observability\OtelInitialization;

if (getenv('OTEL_PHP_AUTOLOAD_ENABLED') === 'true' && false === defined('app.otel')) {
    define('app.otel', true);
    OtelInitialization::registerHooks();
}
