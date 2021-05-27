<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

/**
 * https://github.com/PedroTroller/PhpCSFixer-Custom-Fixers/issues/139.
 */
final class Case6 implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'max-args'                 => false,
            'max-length'               => 1,
            'automatic-argument-merge' => false,
        ]);

        yield $fixer;
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            interface Foo {
                public function bar(
                    string $path
                ): string;
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
            <?php

            interface Foo {
                public function bar(
                    string $path
                ): string;
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
