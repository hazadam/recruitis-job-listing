<?php

declare(strict_types=1);

namespace App\Observability;

use OpenTelemetry\API\Instrumentation\CachedInstrumentation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use function in_array;
use function is_float;
use function is_string;
use function microtime;
use function OpenTelemetry\Instrumentation\hook;
use function round;

class OtelInitialization
{
    private const array BLACKLISTED_ROUTES = ['health_check'];
    private const string REQ_START = '_otel_req_start';

    public static function registerHooks(): void
    {
        hook(
            HttpKernel::class,
            'handle',
            pre: static function (
                HttpKernel $kernel,
                array $params,
            ): void {
                $request = ($params[0] instanceof Request) ? $params[0] : null;
                $type = $params[1] ?? null;

                if ($type !== HttpKernelInterface::MAIN_REQUEST) {
                    return;
                }

                $request?->attributes->set(self::REQ_START, microtime(true));
            },
            post: static function (
                HttpKernel $kernel,
                array $params,
                mixed $response,
            ): void {
                $request = ($params[0] instanceof Request) ? $params[0] : null;

                if (false === $request instanceof Request ||
                    false === is_float($request->attributes->get(self::REQ_START))
                ) {
                    return;
                }

                $route = $request->attributes->get('_route');

                if (false === is_string($route) ||
                    in_array($route, self::BLACKLISTED_ROUTES, true)
                ) {
                    return;
                }

                $instrumentation = new CachedInstrumentation('com.job-listing');
                $labels = [
                    'handler' => $route,
                    'method' => $request->getMethod(),
                ];

                if ($response instanceof Response) {
                    $labels['status'] = $response->getStatusCode();
                }

                $histogramName = 'http_server_duration';
                $now = microtime(true);
                $then = $request->attributes->get(self::REQ_START);

                $latency = round(($now - $then) * 1000);
                $instrumentation->meter()->createHistogram($histogramName, 'ms')->record($latency, $labels);

                $counter = $instrumentation->meter()->createCounter('http_requests');
                $counter->add(1, $labels);
            },
        );
    }
}
