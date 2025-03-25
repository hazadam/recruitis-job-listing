<?php

declare(strict_types=1);

namespace App\RecruitisApi;

final readonly class Response
{
    public function __construct(
        public int $statusCode,
        public string $content,
        /** @var array<string, list<string|null>> $headers */
        public array $headers,
    ) {
    }
}
