<?php

declare(strict_types=1);

namespace tests\UseCase\Phpspec\Regression;

use PedroTroller\CS\Fixer\PhpspecFixer;
use tests\Fixture;
use tests\UseCase;

final class Case2 implements UseCase
{
    public function getFixers(): iterable
    {
        yield new PhpspecFixer();
    }

    public function getRawScript(): string
    {
        return Fixture::read(\sprintf('%s/Case2/file.php.txt', __DIR__));
    }

    public function getExpectation(): string
    {
        return Fixture::read(\sprintf('%s/Case2/file.php.txt', __DIR__));
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
