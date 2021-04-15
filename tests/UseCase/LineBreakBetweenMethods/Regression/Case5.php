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
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'max-args'   => 4,
            'max-length' => 100,
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

return [\dirname(__DIR__) . '/definitions'];
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
    {
        return <<<'PHP'
<?php

return [\dirname(__DIR__) . '/definitions'];
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
