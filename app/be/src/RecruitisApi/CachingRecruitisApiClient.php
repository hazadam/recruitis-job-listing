<?php

declare(strict_types=1);

namespace App\RecruitisApi;

use InvalidArgumentException;
use Override;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use function App\Functions\tryCatch;
use function hash;
use function is_array;
use function is_string;
use function ksort;
use function parse_str;
use function strlen;

readonly class CachingRecruitisApiClient implements RecruitisApiClientInterface
{
    public function __construct(
        private CacheItemPoolInterface      $cache,
        private RecruitisApiClientInterface $liveClient,
        private LoggerInterface             $logger,
        private int                         $cacheLifetimeInSeconds,
    ) {
    }

    /**
     * @param mixed[]|null $body
     * @param array<string, list<string|null>> $headers
     */
    #[Override]
    public function request(
        string $method,
        string $path,
        ?string $queryString,
        ?array $body,
        array $headers
    ): Response {
        $fallback = fn (): Response => $this->liveClient->request(
            $method,
            $path,
            $queryString,
            $body,
            $headers
        );

        if (Request::METHOD_GET !== $method || self::skipCaching($queryString)) {
            return $fallback();
        }

        /** @var string $cacheKey */
        [$cacheKey, $throwable] = tryCatch(function () use ($method, $path, $queryString): string {
            $queryString ??= '';
            $queryStringParameters = [];
            parse_str($queryString, $queryStringParameters);
            self::sortQueryStringParameters($queryStringParameters);
            $queryString = self::stringifyQueryStringParameters($queryStringParameters);

            return hash('sha256', $method . $path . $queryString);
        });

        if (null !== $throwable) {
            $this->logger->error(
                'Error during cache key calculation due to: ' . $throwable->getMessage(),
                ['throwable' => $throwable]
            );

            return $fallback();
        }

        unset($throwable);
        /** @var CacheItemInterface $cacheItem */
        [$cacheItem, $throwable] = tryCatch(fn (): CacheItemInterface => $this->cache->getItem($cacheKey));

        if (null !== $throwable) {
            $this->logger->error(
                'Error during cache retrieval due to: ' . $throwable->getMessage(),
                ['throwable' => $throwable]
            );

            return $fallback();
        }

        unset($throwable);

        if ($cacheItem->isHit()) {
            $response = $cacheItem->get();

            if (false === $response instanceof Response) {
                $this->logger->error('Cache contained something other than the Response object.');
                $cacheItem->expiresAfter(0);
                $this->cache->save($cacheItem);
                $response = $fallback();
            }
        } else {
            $response = $fallback();
            $cacheItem->set($response);
            $cacheItem->expiresAfter($this->cacheLifetimeInSeconds);
            [$saved, $throwable] = tryCatch(fn (): bool => $this->cache->save($cacheItem));

            if (false === $saved || null !== $throwable) {
                $this->logger->error(
                    /** @phpstan-ignore-next-line  */
                    'Response cannot be saved due to: ' . $throwable?->getMessage() ?? 'Unknown error',
                    null !== $throwable ? ['throwable' => $throwable] : [],
                );
            }
        }

        return $response;
    }

    /**
     * @param array<int|string, mixed> $queryStringParameters
     */
    private static function sortQueryStringParameters(array &$queryStringParameters): void
    {
        ksort($queryStringParameters);

        foreach ($queryStringParameters as $parameter) {
            if (is_array($parameter)) {
                self::sortQueryStringParameters($parameter);
            }
        }
    }

    private static function skipCaching(?string $queryString): bool
    {
        return strlen($queryString ?? '') >= 2_048;
    }

    /**
     * @param array<int|string, mixed> $queryStringParameters
     */
    private static function stringifyQueryStringParameters(array $queryStringParameters): string
    {
        $result = '';

        foreach ($queryStringParameters as $key => $value) {
            $result .= $key;

            if (is_array($value)) {
                $result .= self::stringifyQueryStringParameters($value);
            } elseif (is_string($value)) {
                $result .= $value;
            } else {
                throw new InvalidArgumentException('Query string values are not expected to be anything other than strings');
            }
        }

        return $result;
    }
}
