<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

final class LineBreakBetweenMethods implements UseCase
{
    public function getFixers(): iterable
    {
        yield new LineBreakBetweenMethodArgumentsFixer();
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

                public function fun3()
                {
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

                public function php70($arg1, array $arg2 = [], \ArrayAccess $arg3 = null, $foo = 'bar'): bool
                {
                }

                public function php71($arg1, array $arg2 = [], \ArrayAccess $arg3 = null, $foo = 'bar'): ? bool
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
                public function fun1(
                    $arg1,
                    array $arg2 = [],
                    \ArrayAccess $arg3 = null,
                    $foo = 'bar'
                ) {
                    return;
                }

                public function fun2($arg1, array $arg2 = [])
                {
                    return;
                }

                public function fun3()
                {
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

                public function php70(
                    $arg1,
                    array $arg2 = [],
                    \ArrayAccess $arg3 = null,
                    $foo = 'bar'
                ): bool {
                }

                public function php71(
                    $arg1,
                    array $arg2 = [],
                    \ArrayAccess $arg3 = null,
                    $foo = 'bar'
                ): ? bool {
                }
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 70100;
    }
}
