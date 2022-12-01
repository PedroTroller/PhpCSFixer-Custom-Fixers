<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
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
     * @param array<
     *     array{
     *         start: int,
     *         visibility: string,
     *         static: bool,
     *         comment: ?string,
     *         type?: string,
     *         methodName?: string,
     *         propertyName?: string,
     *         end?: int
     *     }
     * > $elements
     *
     * @return array<
     *     array{
     *         start: int,
     *         visibility: string,
     *         static: bool,
     *         comment: ?string,
     *         type?: string,
     *         methodName?: string,
     *         propertyName?: string,
     *         end?: int
     *     }
     * >
     */
    abstract protected function sortElements(array $elements): array;

    /**
     * @return array<
     *     array{
     *         start: int,
     *         visibility: string,
     *         static: bool,
     *         comment: ?string,
     *         type?: string,
     *         methodName?: string,
     *         propertyName?: string,
     *         end?: int
     *     }
     * >
     */
    private function getElements(Tokens $tokens, int $startIndex)
    {
        static $elementTokenKinds = [CT::T_USE_TRAIT, T_CONST, T_VARIABLE, T_FUNCTION];

        ++$startIndex;
        $elements = [];

        while (true) {
            $element = [
                'start'      => $startIndex,
                'visibility' => 'public',
                'static'     => false,
                'comment'    => null,
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
            }

            $elements[] = $element;
            $startIndex = $element['end'] + 1;
        }
    }

    /**
     * @return array{string, string}|string
     */
    private function detectElementType(Tokens $tokens, int $index)
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

    private function findElementEnd(Tokens $tokens, int $index): int
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
     * @param array<
     *     array{
     *         start: int,
     *         visibility: string,
     *         static: bool,
     *         comment: ?string,
     *         type?: string,
     *         methodName?: string,
     *         propertyName?: string,
     *         end?: int
     *     }
     * > $elements
     */
    private function sortTokens(
        Tokens $tokens,
        int $startIndex,
        int $endIndex,
        array $elements
    ): void {
        /**
         * @var Token[]
         */
        $replaceTokens = [];

        foreach ($elements as $element) {
            for ($i = $element['start']; $i <= $element['end']; ++$i) {
                $replaceTokens[] = clone $tokens[$i];
            }
        }

        $tokens->overrideRange($startIndex + 1, $endIndex, $replaceTokens);
    }
}
