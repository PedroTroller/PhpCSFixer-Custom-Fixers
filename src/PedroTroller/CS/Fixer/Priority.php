<?php

namespace PedroTroller\CS\Fixer;

final class Priority
{
    private function __construct()
    {
    }

    /**
     * @param array<int,mixed> $classes
     *
     * @return int
     */
    public static function before(...$classes)
    {
        $priorities = array_map(
            function ($class) {
                return (new $class())->getPriority();
            },
            $classes
        );

        return max($priorities) + 1;
    }

    /**
     * @param array<int,mixed> $classes
     *
     * @return int
     */
    public static function after(...$classes)
    {
        $priorities = array_map(
            function ($class) {
                return (new $class())->getPriority();
            },
            $classes
        );

        return min($priorities) - 1;
    }
}
