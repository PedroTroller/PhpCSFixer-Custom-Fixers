<?php

declare(strict_types=1);

namespace tests\UseCase\UselessCodeAfterReturn\Regression;

use PedroTroller\CS\Fixer\DeadCode\UselessCodeAfterReturnFixer;
use tests\Fixture;
use tests\UseCase;

final class Case1 implements UseCase
{
    public function getFixers(): iterable
    {
        yield new UselessCodeAfterReturnFixer();
    }

    public function getRawScript(): string
    {
        return Fixture::read(__DIR__.'/Case1/file.php.txt');
    }

    public function getExpectation(): string
    {
        return Fixture::read(__DIR__.'/Case1/file.php.txt');
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 70000;
    }
}
