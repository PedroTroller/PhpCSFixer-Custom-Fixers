<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

final class LineBreakBetweenMethodsWithNoSplitOnNumberOfArgs implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'max-args'   => false,
            'max-length' => 90,
        ]);

        yield $fixer;
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            namespace Project\TheNamespace;

            class TheClass
            {
                public function fun1($arg1, array $arg2 = [], \ArrayAccess $arg3 = null, $foo = 'bar')
                {
                    return;
                }

                public function fun2(
                    $arg1,
                    array $arg2 = []
                ) {
                    return;
                }

                public function fun4($foo, $bar, $bar, $boolean = true, $integer = 1, $string = 'string')
                {
                }
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
            <?php

            namespace Project\TheNamespace;

            class TheClass
            {
                public function fun1($arg1, array $arg2 = [], \ArrayAccess $arg3 = null, $foo = 'bar')
                {
                    return;
                }

                public function fun2($arg1, array $arg2 = [])
                {
                    return;
                }

                public function fun4(
                    $foo,
                    $bar,
                    $bar,
                    $boolean = true,
                    $integer = 1,
                    $string = 'string'
                ) {
                }
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 70100;
    }
}
