<?php

declare(strict_types=1);

namespace App\RecruitisApi;

interface RecruitisApiClientInterface
{
    /**
     * @param mixed[]|null $body
     * @param array<string, list<string|null>> $headers
     */
    public function request(
        string $method,
        string $path,
        ?string $queryString,
        ?array $body,
        array $headers
    ): Response;
}
