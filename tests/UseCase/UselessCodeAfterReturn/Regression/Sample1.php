<?php

declare(strict_types=1);

namespace tests\UseCase\UselessCodeAfterReturn\Regression;

use tests\UseCase;
use PedroTroller\CS\Fixer\DeadCode\UselessCodeAfterReturnFixer;

final class Sample1 implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        return new UselessCodeAfterReturnFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
    {
        return file_get_contents(__DIR__ . '/Sample1/file.php');
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
    {
        return file_get_contents(__DIR__ . '/Sample1/file.php');
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 70000;
    }
}
