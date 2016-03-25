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

            if ('}' !== $token->getContent()) {
                continue;
            }

            $space = $tokens[$index + 1];

            if (false === $space->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $statement = $tokens[$index + 2];

            switch ($statement->getId()) {
                case T_IF:
                case T_DO:
                case T_FOREACH:
                case T_FOR:
                case T_WHILE:
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
