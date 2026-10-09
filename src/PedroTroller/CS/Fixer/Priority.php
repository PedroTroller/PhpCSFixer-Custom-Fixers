<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use PhpCsFixer\Fixer\FixerInterface;

final class Priority
{
    private function __construct() {}

    /**
     * @param class-string<FixerInterface> $class
     * @param class-string<FixerInterface> ...$classes
     *
     * @return int
     */
    public static function before(string $class, string ...$classes)
    {
        $priorities = array_map(
            static fn ($class) => (new $class())->getPriority(),
            [$class, ...$classes]
        );

        return max($priorities) + 1;
    }

    /**
     * @param class-string<FixerInterface> $class
     * @param class-string<FixerInterface> ...$classes
     *
     * @return int
     */
    public static function after(string $class, string ...$classes)
    {
        $priorities = array_map(
            static fn ($class) => (new $class())->getPriority(),
            [$class, ...$classes]
        );

        return min($priorities) - 1;
    }
}
