<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

class PhpspecScenarioNameUnderscorecaseFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        if (false === $this->extendsClass($tokens, 'PhpSpec\ObjectBehavior')) {
            return $content;
        }

        foreach ($tokens->getClassyElements() as $index => $element) {
            $next = $tokens->getNextNonWhitespace($index);

            if (T_FUNCTION !== $element['token']->getId()) {
                continue;
            }

            $name = $tokens[$next];

            if (false === $this->startsWith($name->getContent(), 'it_')) {
                continue;
            }

            $name->setContent(strtolower(preg_replace('/(?!^)([A-Z])/', '_$1', $name->getContent())));
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return <<<DESCRIPTION
[PHPSPEC]

This fixer will ensure that your spec functions names will always be underscore cased.
DESCRIPTION;
    }
}
