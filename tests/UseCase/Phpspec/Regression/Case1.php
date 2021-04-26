<?php

declare(strict_types=1);

namespace tests\UseCase\Phpspec\Regression;

use PedroTroller\CS\Fixer\PhpspecFixer;
use tests\UseCase;

final class Case1 implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new PhpspecFixer();

        $fixer->configure([
            'instanceof' => ['Funk\Spec'],
        ]);

        yield $fixer;
    }

    public function getRawScript(): string
    {
        return file_get_contents(sprintf('%s/Case1/file.php.txt', __DIR__));
    }

    public function getExpectation(): string
    {
        return file_get_contents(sprintf('%s/Case1/file.php.txt', __DIR__));
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
