<?php

declare(strict_types=1);

namespace tests\UseCase\Phpspec\Regression;

use PedroTroller\CS\Fixer\PhpspecFixer;
use tests\UseCase;

final class Case1 implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        $fixer = new PhpspecFixer();

        $fixer->configure([
            'instanceof' => ['Funk\Spec'],
        ]);

        return $fixer;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
    {
        return file_get_contents(sprintf('%s/Case1/file.php.txt', __DIR__));
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
    {
        return file_get_contents(sprintf('%s/Case1/file.php.txt', __DIR__));
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }
}
