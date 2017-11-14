<?php

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use PhpCsFixer\Fixer\FixerInterface;
use tests\UseCase;

class Case1 implements UseCase
{
    public function getFixer(): FixerInterface
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'max-args'   => 4,
            'max-length' => 80,
        ]);

        return $fixer;
    }

    public function getRawScript(): string
    {
        return file_get_contents(sprintf('%s/Case1/CamelizeNamingStrategy.php.text', __DIR__));
    }

    public function getExpectation(): string
    {
        return file_get_contents(sprintf('%s/Case1/CamelizeNamingStrategy.php.text', __DIR__));
    }
}
