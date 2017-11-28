<?php

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use PhpCsFixer\Fixer\FixerInterface;
use tests\UseCase;

class Case2 implements UseCase
{
    public function getFixer(): FixerInterface
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'max-args'   => 4,
            'max-length' => 80,
        ]);

        return $fixer;
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
<?php

namespace Project\TheNamespace;

class TheClass
{
    public function thisIsAVeryLongMethodWithALengthHightThenFourtyCharsButNoArguments()
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
    public function thisIsAVeryLongMethodWithALengthHightThenFourtyCharsButNoArguments()
    {
    }
}
PHP;
    }
}
