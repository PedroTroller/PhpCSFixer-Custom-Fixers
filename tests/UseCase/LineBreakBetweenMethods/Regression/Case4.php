<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

/**
 * https://github.com/PedroTroller/PhpCSFixer-Custom-Fixers/issues/104.
 */
final class Case4 implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new LineBreakBetweenMethodArgumentsFixer();

        $fixer->configure([
            'max-args'   => 4,
            'max-length' => 100,
        ]);

        yield $fixer;
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            // error, because line is too long and fixer will try to split it
            function addDateIntervalaaaaaaaaaaaaaakjfklsdjfklsjfkjklsajlkkldfjklajlkfjaslfjskdfjksajlkfjldaj(DateTimeInterface $date): void
            {
                echo 'do something';
            }

            //its ok, because we have spaces before function
                function addDateIntervalaaaaaaaaaaaaaakjfklsdjfklsjfkjklsajlkkldfjklajlkfjaslfjskdfjksajlkfjldaj(DateTimeInterface $date): void
                {
                    echo 'do something';
                }

            //its ok, because we do not have return type
            function addDateIntervalaaaaaaaaaaaaaakjfklsdjfklsjfkjklsajlkkldfjklajlkfjaslfjskdfjksajlkfjldaj(DateTimeInterface $date): void
            {
                echo 'do something';
            }

            //is ok, because function name less than 100 chars and fixer do not try to split it
            function addDateInterval(DateTimeInterface $date): void
            {
                echo 'do something';
            }

            //error
            function someFuncName(
                string $format,
                string $time,
                DateTimeZone $timezone = null
            ): void {
                echo 'do something';
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
            <?php

            // error, because line is too long and fixer will try to split it
            function addDateIntervalaaaaaaaaaaaaaakjfklsdjfklsjfkjklsajlkkldfjklajlkfjaslfjskdfjksajlkfjldaj(
                DateTimeInterface $date
            ): void {
                echo 'do something';
            }

            //its ok, because we have spaces before function
                function addDateIntervalaaaaaaaaaaaaaakjfklsdjfklsjfkjklsajlkkldfjklajlkfjaslfjskdfjksajlkfjldaj(
                    DateTimeInterface $date
                ): void {
                    echo 'do something';
                }

            //its ok, because we do not have return type
            function addDateIntervalaaaaaaaaaaaaaakjfklsdjfklsjfkjklsajlkkldfjklajlkfjaslfjskdfjksajlkfjldaj(
                DateTimeInterface $date
            ): void {
                echo 'do something';
            }

            //is ok, because function name less than 100 chars and fixer do not try to split it
            function addDateInterval(DateTimeInterface $date): void
            {
                echo 'do something';
            }

            //error
            function someFuncName(string $format, string $time, DateTimeZone $timezone = null): void
            {
                echo 'do something';
            }
            PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 70000;
    }
}
