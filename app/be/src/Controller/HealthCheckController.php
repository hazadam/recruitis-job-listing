<?php

declare(strict_types=1);

namespace App\Controller;

use App\RecruitisApi\RecruitisApiClientInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function App\Functions\any;
use function App\Functions\tryCatch;
use function is_object;

final class HealthCheckController extends AbstractController
{
    public function __construct(
        private readonly RecruitisApiClientInterface $apiClient,
        private readonly CacheItemPoolInterface      $cache,
    ) {
    }

    #[Route('/_health', name: 'health_check')]
    public function healthAction(): Response
    {
        [, $apiMalfunction] = tryCatch(
            fn () => $this->apiClient->request(Request::METHOD_GET, '/me', null, null, [])
        );
        [, $cacheMalfunction] = tryCatch(
            fn () => $this->cache->hasItem('health_check'),
        );

        return new Response(
            status: any([$apiMalfunction, $cacheMalfunction], is_object(...))
                ? Response::HTTP_I_AM_A_TEAPOT
                : Response::HTTP_OK
        );
    }
}
