<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

abstract class AbstractOrderedClassElementsFixer extends AbstractFixer
{
    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        for ($i = 1, $count = $tokens->count(); $i < $count; ++$i) {
            if (!$tokens[$i]->isClassy()) {
                continue;
            }

            $i        = $tokens->getNextTokenOfKind($i, ['{']);
            $elements = $this->getElements($tokens, $i);

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
     * @param array[] $elements
     *
     * @return array[]
     */
    abstract protected function sortElements(array $elements): array;

    /**
     * @param int $startIndex
     *
     * @return array[]
     */
    private function getElements(Tokens $tokens, $startIndex)
    {
        static $elementTokenKinds = [CT::T_USE_TRAIT, T_CONST, T_VARIABLE, T_FUNCTION];

        ++$startIndex;
        $elements = [];

        while (true) {
            $element = [
                'start'      => $startIndex,
                'visibility' => 'public',
                'static'     => false,
            ];

            for ($i = $startIndex;; ++$i) {
                $token = $tokens[$i];

                if ($token->equals('}')) {
                    return $elements;
                }

                if ($token->isGivenKind(T_STATIC)) {
                    $element['static'] = true;

                    continue;
                }

                if ($token->isGivenKind([T_PROTECTED, T_PRIVATE])) {
                    $element['visibility'] = mb_strtolower($token->getContent());

                    continue;
                }

                if (!$token->isGivenKind($elementTokenKinds)) {
                    continue;
                }

                $type            = $this->detectElementType($tokens, $i);
                $element['type'] = $type;

                switch ($type) {
                    case 'method':
                        $element['methodName'] = $tokens[$tokens->getNextMeaningfulToken($i)]->getContent();

                        break;

                    case 'property':
                        $element['propertyName'] = $token->getContent();

                        break;
                }
                $element['end'] = $this->findElementEnd($tokens, $i);

                break;
            }

            $possibleCommentIndex = $startIndex + 1;

            if (isset($tokens[$possibleCommentIndex]) && $tokens[$possibleCommentIndex]->isComment()) {
                $element['comment'] = $tokens[$possibleCommentIndex]->getContent();
            } else {
                $element['comment'] = null;
            }

            $elements[] = $element;
            $startIndex = $element['end'] + 1;
        }
    }

    /**
     * @param int $index
     *
     * @return array|string type or array of type and name
     */
    private function detectElementType(Tokens $tokens, $index)
    {
        $token = $tokens[$index];

        if ($token->isGivenKind(CT::T_USE_TRAIT)) {
            return 'use_trait';
        }

        if ($token->isGivenKind(T_CONST)) {
            return 'constant';
        }

        if ($token->isGivenKind(T_VARIABLE)) {
            return 'property';
        }

        $nameToken = $tokens[$tokens->getNextMeaningfulToken($index)];

        if ($nameToken->equals([T_STRING, '__construct'], false)) {
            return 'construct';
        }

        if ($nameToken->equals([T_STRING, '__destruct'], false)) {
            return 'destruct';
        }

        if (
            $nameToken->equalsAny([
                [T_STRING, 'setUpBeforeClass'],
                [T_STRING, 'tearDownAfterClass'],
                [T_STRING, 'setUp'],
                [T_STRING, 'tearDown'],
            ], false)
        ) {
            return ['phpunit', mb_strtolower($nameToken->getContent())];
        }

        if ('__' === mb_substr($nameToken->getContent(), 0, 2)) {
            return 'magic';
        }

        return 'method';
    }

    /**
     * @param int $index
     *
     * @return int
     */
    private function findElementEnd(Tokens $tokens, $index)
    {
        $index = $tokens->getNextTokenOfKind($index, ['{', ';']);

        if ($tokens[$index]->equals('{')) {
            $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        }

        for (++$index; $tokens[$index]->isWhitespace(" \t") || $tokens[$index]->isComment(); ++$index);

        --$index;

        return $tokens[$index]->isWhitespace() ? $index - 1 : $index;
    }

    /**
     * @param int     $startIndex
     * @param int     $endIndex
     * @param array[] $elements
     */
    private function sortTokens(
        Tokens $tokens,
        $startIndex,
        $endIndex,
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
