<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

final class Case1 implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'max-args'   => 4,
            'max-length' => 80,
        ]);

        yield $fixer;
    }

    public function getRawScript(): string
    {
        return file_get_contents(sprintf('%s/Case1/CamelizeNamingStrategy.php.text', __DIR__));
    }

    public function getExpectation(): string
    {
        return file_get_contents(sprintf('%s/Case1/CamelizeNamingStrategy.php.text', __DIR__));
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 70100;
    }
}
