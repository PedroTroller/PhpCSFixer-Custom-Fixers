<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

final class Priority
{
    private function __construct() {}

    /**
     * @param array<int, mixed> $classes
     *
     * @return int
     */
    public static function before(...$classes)
    {
        $priorities = array_map(
            static fn ($class) => (new $class())->getPriority(),
            $classes
        );

        return max($priorities) + 1;
    }

    /**
     * @param array<int, mixed> $classes
     *
     * @return int
     */
    public static function after(...$classes)
    {
        $priorities = array_map(
            static fn ($class) => (new $class())->getPriority(),
            $classes
        );

        return min($priorities) - 1;
    }
}
