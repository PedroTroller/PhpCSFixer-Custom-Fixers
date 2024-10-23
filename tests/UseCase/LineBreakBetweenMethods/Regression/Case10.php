<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

/**
 * https://github.com/PedroTroller/PhpCSFixer-Custom-Fixers/issues/208
 * https://github.com/PedroTroller/PhpCSFixer-Custom-Fixers/issues/214.
 */
final class Case10 implements UseCase
{
    public function getFixers(): iterable
    {
        yield new LineBreakBetweenMethodArgumentsFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            class Foo
            {
                public function __construct(
                    // simple comment
                    public ?string $simpleCommentAbove = null,
                    // public string|null $commentedOutLine = null,
                    public ?string $detailAndEdit = null,
                    /**
                     * @var list<string>
                     */
                    public array $groups = [])
                {
                }
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return $this->getRawScript();
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 80000;
    }
}
