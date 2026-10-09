<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

/**
 * A trailing comma must not shift the indentation of the closing parenthesis.
 */
final class Case11 implements UseCase
{
    public function getFixers(): iterable
    {
        yield new LineBreakBetweenMethodArgumentsFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            class Foo
            {
                public function bar($first, $second, $third, $fourth,)
                {
                }

                public function baz(
                    $first,
                    $second,
                    $third,
                    $fourth,
                ) {
                }
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
            <?php

            class Foo
            {
                public function bar(
                    $first,
                    $second,
                    $third,
                    $fourth,
                ) {
                }

                public function baz(
                    $first,
                    $second,
                    $third,
                    $fourth,
                ) {
                }
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 80000;
    }
}
