<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

class Case1 implements UseCase
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
        return file_get_contents(sprintf('%s/Case1/CamelizeNamingStrategy.php.text', __DIR__));
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
    {
        return file_get_contents(sprintf('%s/Case1/CamelizeNamingStrategy.php.text', __DIR__));
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 70100;
    }
}
