<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

/**
 * https://github.com/PedroTroller/PhpCSFixer-Custom-Fixers/issues/131.
 */
final class Case5 implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'max-args'   => 4,
            'max-length' => 100,
        ]);

        yield $fixer;
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            return [\dirname(__DIR__) . '/definitions'];
            PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
            <?php

            return [\dirname(__DIR__) . '/definitions'];
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
