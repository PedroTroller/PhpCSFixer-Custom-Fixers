<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

class PhpspecScenarioScopeFixer extends AbstractFixer
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

        foreach (array_reverse($tokens->getClassyElements(), true) as $index => $element) {
            $next     = $tokens->getNextNonWhitespace($index);
            $previous = $tokens->getPrevNonWhitespace($index);

            if (T_FUNCTION !== $element['token']->getId()) {
                continue;
            }

            if (null === $previous) {
                continue;
            }

            $name  = $tokens[$next];
            $scope = $tokens[$previous];

            if (false === in_array($name->getContent(), array('let', 'letGo'))
                && false === $this->startsWith($name->getContent(), 'it_')) {
                continue;
            }

            if (T_PUBLIC !== $scope->getId()) {
                continue;
            }

            $tokens->overrideAt($previous, '');
            $tokens->removeTrailingWhitespace($previous);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHPSpec spec functions MUST NOT have a public scope.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -250;
    }
}
