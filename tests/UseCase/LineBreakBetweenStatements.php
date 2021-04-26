<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenStatementsFixer;
use tests\UseCase;

final class LineBreakBetweenStatements implements UseCase
{
    public function getFixers(): iterable
    {
        yield new LineBreakBetweenStatementsFixer();
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


        while($forever = false) {
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

        while($forever = false) {
        }
    }
}';
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
