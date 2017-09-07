<?php

namespace tests\UseCase;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenStatementsFixer;
use PhpCsFixer\Fixer\FixerInterface;
use tests\UseCase;

class LineBreakBetweenStatements implements UseCase
{
    public function getFixer(): FixerInterface
    {
        return new LineBreakBetweenStatementsFixer();
    }

    public function getRawScript(): string
    {
        return '
<?php
class TheClass
{
    public function theFunction()
    {
        do {
            //yolo
        } while (true);
        if (true) {
            return;
        }
        foreach ([] as $nothing) {
            continue;
        }
        while($forever = true) {
        }
    }
}';
    }

    public function getExpectation(): string
    {
        return '
<?php
class TheClass
{
    public function theFunction()
    {
        do {
            //yolo
        } while (true);

        if (true) {
            return;
        }

        foreach ([] as $nothing) {
            continue;
        }

        while($forever = true) {
        }
    }
}';
    }
}
