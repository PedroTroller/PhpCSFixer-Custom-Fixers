<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\CodingStyle\ExceptionsPunctuationFixer;
use tests\UseCase;

final class ExceptionsPunctuation implements UseCase
{
    public function getFixers(): iterable
    {
        yield new ExceptionsPunctuationFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            use LogicException;
            use RuntimeException;

            class MyClass {
                public function fun1()
                {
                    throw new \Exception('This is the message');
                }

                public function fun2($data)
                {
                    throw new LogicException(sprintf('This is the %s', 'message'));
                }

                public function fun3($data)
                {
                    throw new RuntimeException('This is the '.message);
                }

                public function fun4($data)
                {
                    throw new RuntimeException('Are you sure ?');
                }
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
            <?php

            use LogicException;
            use RuntimeException;

            class MyClass {
                public function fun1()
                {
                    throw new \Exception('This is the message.');
                }

                public function fun2($data)
                {
                    throw new LogicException(sprintf('This is the %s.', 'message'));
                }

                public function fun3($data)
                {
                    throw new RuntimeException('This is the '.message);
                }

                public function fun4($data)
                {
                    throw new RuntimeException('Are you sure ?');
                }
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
