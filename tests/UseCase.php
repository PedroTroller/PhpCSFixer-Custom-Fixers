<?php

namespace tests;

use PhpCsFixer\Fixer\FixerInterface;

interface UseCase
{
    public function getFixer(): FixerInterface;

    public function getRawScript(): string;

    public function getExpectation(): string;
}
