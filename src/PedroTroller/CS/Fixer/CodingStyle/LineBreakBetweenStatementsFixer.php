<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\CodingStyle;

use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class LineBreakBetweenStatementsFixer extends AbstractFixer
{
    /**
     * @var array<int, string>
     */
    private const HANDLERS = [
        T_DO      => 'do',
        T_FOR     => 'common',
        T_FOREACH => 'common',
        T_IF      => 'common',
        T_SWITCH  => 'common',
        T_WHILE   => 'common',
    ];

    public function getSampleCode(): string
    {
        return <<<'PHP'
            <?php

            namespace Project\TheNamespace;

            class TheClass
            {
                /**
                 * @return null
                 */
                public function fun() {
                    do {
                        // ...
                    } while (true);
                    foreach (['foo', 'bar'] as $str) {
                        // ...
                    }
                    if (true === false) {
                        // ...
                    }


                    while (true) {
                        // ...
                    }
                }
            }
            PHP;
    }

    public function getDocumentation(): string
    {
        return 'Each statement (in, for, foreach, ...) MUST BE separated by an empty line';
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens->findGivenKind(array_keys(self::HANDLERS)) as $kind => $matchedTokens) {
            $this->{'handle'.ucfirst(self::HANDLERS[$kind])}($matchedTokens, $tokens);
        }
    }

    private function handleDo(array $matchedTokens, Tokens $tokens): void
    {
        foreach ($matchedTokens as $index => $token) {
            $this->fixSpaces(
                $this->analyze($tokens)->getNextSemiColon($index),
                $tokens
            );
        }
    }

    private function handleCommon(array $matchedTokens, Tokens $tokens): void
    {
        foreach ($matchedTokens as $index => $token) {
            $curlyBracket = $tokens->findSequence([
                '{',
            ], $index);

            if (empty($curlyBracket)) {
                continue;
            }

            $openCurlyBracket = current(array_keys($curlyBracket));

            if (false === $openCurlyBracket) {
                continue;
            }

            $closeCurlyBracket = $this->analyze($tokens)->getClosingCurlyBracket($openCurlyBracket);

            if (null === $closeCurlyBracket) {
                continue;
            }

            $this->fixSpaces(
                $closeCurlyBracket,
                $tokens
            );
        }
    }

    private function fixSpaces($index, Tokens $tokens): void
    {
        $space = $index + 1;

        if (false === $tokens[$space]->isWhitespace()) {
            return;
        }

        $nextMeaningful = $tokens->getNextMeaningfulToken($index);

        if (null === $nextMeaningful) {
            return;
        }

        if (false === \array_key_exists($tokens[$nextMeaningful]->getId(), self::HANDLERS)) {
            return;
        }

        $tokens[$space] = new Token([T_WHITESPACE, $this->ensureNumberOfBreaks($tokens[$space]->getContent())]);
    }

    private function ensureNumberOfBreaks($whitespace)
    {
        $parts = explode("\n", $whitespace);

        while (3 < \count($parts)) {
            array_shift($parts);
        }

        while (3 > \count($parts)) {
            array_unshift($parts, '');
        }

        return implode("\n", $parts);
    }
}
