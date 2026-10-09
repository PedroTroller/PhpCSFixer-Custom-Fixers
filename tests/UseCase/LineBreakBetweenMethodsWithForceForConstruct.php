<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

final class LineBreakBetweenMethodsWithForceForConstruct implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'force-for-construct' => true,
        ]);

        yield $fixer;
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            namespace Project\TheNamespace;

            class Version
            {
                public function __construct(public string $version)
                {
                }

                public function fun1($arg1, array $arg2 = [])
                {
                }

                public function fun2(
                    $arg1
                ) {
                }
            }

            class Point
            {
                public function __construct(private int $x, private int $y)
                {
                }
            }

            class AlreadySplit
            {
                public function __construct(
                    private string $name,
                ) {
                }
            }

            class Uppercase
            {
                public function __CONSTRUCT($arg1)
                {
                }
            }

            class WithoutArguments
            {
                public function __construct(
                ) {
                }
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
            <?php

            namespace Project\TheNamespace;

            class Version
            {
                public function __construct(
                    public string $version
                ) {
                }

                public function fun1($arg1, array $arg2 = [])
                {
                }

                public function fun2($arg1)
                {
                }
            }

            class Point
            {
                public function __construct(
                    private int $x,
                    private int $y
                ) {
                }
            }

            class AlreadySplit
            {
                public function __construct(
                    private string $name,
                ) {
                }
            }

            class Uppercase
            {
                public function __CONSTRUCT(
                    $arg1
                ) {
                }
            }

            class WithoutArguments
            {
                public function __construct()
                {
                }
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 80000;
    }
}
