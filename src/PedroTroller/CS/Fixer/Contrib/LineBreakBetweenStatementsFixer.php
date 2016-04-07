<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

class LineBreakBetweenStatementsFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = 0; $index < $tokens->count() - 2; ++$index) {
            $token = $tokens[$index];

            if (false === $token->equals('}')) {
                continue;
            }

            $space = $tokens[$index + 1];

            if (false === $space->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $statement = $tokens[$index + 2];

            switch ($statement->getId()) {
                // If it's a while, isolate the case of do {} while ();
                case T_WHILE:
                    $semicolon = $tokens->getNextTokenOfKind($index + 1, array(';'));
                    $break     = false;

                    if (null !== $semicolon) {
                        $break = true;
                        for ($next = $index + 1; $next < $semicolon; ++$next) {
                            if ($tokens[$next]->equals('{')) {
                                $break = false;
                            }
                        }
                    }

                    if (true === $break) {
                        $nextSpace = $tokens->getNextTokenOfKind($semicolon, array(array(T_WHITESPACE)));

                        if (null !== $nextSpace) {
                            $space = $tokens[$nextSpace];
                        }
                    }
                case T_IF:
                case T_DO:
                case T_FOREACH:
                case T_FOR:
                    $space->setContent($this->ensureNumberOfBreaks($space->getContent()));
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Each statements MUST be separated by a blank line.';
    }

    /**
     * @param string $whitespace
     *
     * @return string
     */
    private function ensureNumberOfBreaks($whitespace)
    {
        $parts = explode("\n", $whitespace);

        while (3 < count($parts)) {
            array_shift($parts);
        }

        while (3 > count($parts)) {
            array_unshift($parts, '');
        }

        return implode("\n", $parts);
    }
}
