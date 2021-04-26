<?php

declare(strict_types=1);

namespace tests;

use PhpCsFixer\Fixer\FixerInterface;

interface UseCase
{
    /**
     * @return iterable<FixerInterface>
     */
    public function getFixers(): iterable;

    public function getRawScript(): string;

    public function getExpectation(): string;

    public function getMinSupportedPhpVersion(): int;
}
