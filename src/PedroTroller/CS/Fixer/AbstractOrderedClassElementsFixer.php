<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * @phpstan-import-type ClassElement from TokensAnalyzer
 */
abstract class AbstractOrderedClassElementsFixer extends AbstractFixer
{
    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($i = 1, $count = $tokens->count(); $i < $count; ++$i) {
            if (!$tokens[$i]->isClassy()) {
                continue;
            }

            $i = $tokens->getNextTokenOfKind($i, ['{']);

            if (null === $i) {
                return;
            }

            $elements = $this->analyze($tokens)->getElements($i);

            if ([] === $elements) {
                continue;
            }

            $sorted   = $this->sortElements($elements);
            $endIndex = $elements[\count($elements) - 1]['end'];

            if ($sorted !== $elements) {
                $this->sortTokens($tokens, $i, $endIndex, $sorted);
            }

            $i = $endIndex;
        }
    }

    /**
     * @param list<ClassElement> $elements
     *
     * @return list<ClassElement>
     */
    abstract protected function sortElements(array $elements): array;

    /**
     * @param list<ClassElement> $elements
     */
    private function sortTokens(
        Tokens $tokens,
        int $startIndex,
        int $endIndex,
        array $elements
    ): void {
        $replaceTokens = [];

        foreach ($elements as $element) {
            for ($i = $element['start']; $i <= $element['end']; ++$i) {
                $replaceTokens[] = clone $tokens[$i];
            }
        }

        $tokens->overrideRange($startIndex + 1, $endIndex, $replaceTokens);
    }
}
