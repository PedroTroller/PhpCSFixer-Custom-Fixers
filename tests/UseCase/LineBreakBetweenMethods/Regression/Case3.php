<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

final class Case3 implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'max-args'   => 4,
            'max-length' => 80,
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
                public function functionReturningAnonymouseClass()
                {
                    return new class('foo', 100) {

                        /**
                         * @var string
                         */
                        private $string;

                        /**
                         * @var string
                         */
                        private $int;

                        public function __construct(string $string, int $int)
                        {
                            $this->string = $string;
                            $this->int = $int;
                        }

                        public function thisIsAVeryLongMethodNameAndItWillBeBreak(string $string, int $int)
                        {
                            return null;
                        }
                    };
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
                public function functionReturningAnonymouseClass()
                {
                    return new class('foo', 100) {

                        /**
                         * @var string
                         */
                        private $string;

                        /**
                         * @var string
                         */
                        private $int;

                        public function __construct(string $string, int $int)
                        {
                            $this->string = $string;
                            $this->int = $int;
                        }

                        public function thisIsAVeryLongMethodNameAndItWillBeBreak(
                            string $string,
                            int $int
                        ) {
                            return null;
                        }
                    };
                }
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 70000;
    }
}
