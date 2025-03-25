<?php

declare(strict_types=1);

namespace App\RecruitisApi;

use Override;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function array_intersect_key;
use function array_map;
use function implode;

final readonly class RecruitisApiClient implements RecruitisApiClientInterface
{
    public function __construct(private HttpClientInterface $recruitisHttpClient)
    {
    }

    /**
     * @param mixed[]|null $body
     * @param array<string, list<string|null>> $headers
     * @throws TransportExceptionInterface
     */
    #[Override]
    public function request(
        string $method,
        string $path,
        ?string $queryString,
        ?array $body,
        array $headers
    ): Response {
        $queryString ??= '';

        $response = $this->recruitisHttpClient->request(
            $method,
            "/api2$path?$queryString",
            [
                'headers' => self::forwardHeaders($headers),
                'json' => $body,
            ]
        );

        return new Response(
            $response->getStatusCode(),
            $response->getContent(throw: false),
            /** @phpstan-ignore-next-line  */
            $response->getHeaders(throw: false)
        );
    }

    /**
     * @param array<string, list<string|null>> $headers
     * @return array<string, string>
     */
    private static function forwardHeaders(array $headers): array
    {
        return array_map(
            static fn (array $values): string => implode(', ', $values),
            array_intersect_key(
                $headers,
                ['accept-encoding' => null, 'accept' => null, 'user-agent' => null, ]
            ),
        );
    }
}
