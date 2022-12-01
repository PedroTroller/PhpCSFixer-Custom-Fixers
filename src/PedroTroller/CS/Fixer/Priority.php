<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use PhpCsFixer\Fixer\FixerInterface;

final class Priority
{
    private function __construct()
    {
    }

    /**
     * @param class-string<FixerInterface> $classes
     */
    public static function before(string ...$classes): int
    {
        $priorities = array_map(
            fn ($class) => (new $class())->getPriority(),
            $classes
        );

        return max($priorities) + 1;
    }

    /**
     * @param class-string<FixerInterface> $classes
     */
    public static function after(string ...$classes): int
    {
        $priorities = array_map(
            fn ($class) => (new $class())->getPriority(),
            $classes
        );

        return min($priorities) - 1;
    }
}
