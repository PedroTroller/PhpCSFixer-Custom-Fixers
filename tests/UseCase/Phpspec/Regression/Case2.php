<?php

declare(strict_types=1);

namespace tests\UseCase\Phpspec\Regression;

use PedroTroller\CS\Fixer\PhpspecFixer;
use tests\UseCase;

final class Case2 implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        return new PhpspecFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
    {
        return file_get_contents(sprintf('%s/Case2/file.php.txt', __DIR__));
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
    {
        return file_get_contents(sprintf('%s/Case2/file.php.txt', __DIR__));
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }
}
