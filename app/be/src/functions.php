<?php

declare(strict_types=1);

namespace App\Functions;

use Throwable;
use function define;
use function defined;

if (false === defined('app.functions')) {
    define('app.functions', 1);

    /**
     * @template T
     * @param callable(): T $callback
     * @return array{T|null, Throwable|null}
     */
    function tryCatch(callable $callback): array
    {
        try {
            return [$callback(), null];
        } catch (Throwable $exception) {
            return [null, $exception];
        }
    }

    /**
     * @template T
     *
     * @param iterable<T>            $iterable
     * @param callable(T): bool|null $condition
     */
    function any(iterable $iterable, ?callable $condition = null): bool
    {
        $condition ??= static fn (mixed $value): bool => (bool) $value;

        foreach ($iterable as $item) {
            if ($condition($item)) {
                return true;
            }
        }

        return false;
    }
}
