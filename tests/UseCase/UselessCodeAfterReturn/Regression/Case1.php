<?php

declare(strict_types=1);

namespace tests\UseCase\UselessCodeAfterReturn\Regression;

use PedroTroller\CS\Fixer\DeadCode\UselessCodeAfterReturnFixer;
use tests\UseCase;

final class Case1 implements UseCase
{
    public function getFixers(): iterable
    {
        yield new UselessCodeAfterReturnFixer();
    }

    public function getRawScript(): string
    {
        return file_get_contents(__DIR__.'/Case1/file.php.txt');
    }

    public function getExpectation(): string
    {
        return file_get_contents(__DIR__.'/Case1/file.php.txt');
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 70000;
    }
}
