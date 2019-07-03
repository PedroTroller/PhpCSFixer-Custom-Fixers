<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

class Case3 implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'max-args'   => 4,
            'max-length' => 80,
        ]);

        return $fixer;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
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

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
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

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 70000;
    }
}
