<?php

declare(strict_types=1);

namespace tests;

use PhpCsFixer\Fixer\FixerInterface;

interface UseCase
{
    /**
     * @return FixerInterface
     */
    public function getFixer();

    /**
     * @return string
     */
    public function getRawScript();

    /**
     * @return string
     */
    public function getExpectation();

    /**
     * @return int
     */
    public function getMinSupportedPhpVersion();
}
