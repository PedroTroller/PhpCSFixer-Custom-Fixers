<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

class Case2 implements UseCase
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
    public function thisIsAVeryLongMethodWithALengthHightThenFourtyCharsButNoArguments()
    {
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
    public function thisIsAVeryLongMethodWithALengthHightThenFourtyCharsButNoArguments()
    {
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }
}
