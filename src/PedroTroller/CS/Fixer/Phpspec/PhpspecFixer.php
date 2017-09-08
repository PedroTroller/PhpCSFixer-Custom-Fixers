<?php

namespace PedroTroller\CS\Fixer\Phpspec;

use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

class PhpspecFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $this->extendsClass($tokens, 'PhpSpec\ObjectBehavior');
    }

    public function getDocumentation(): string
    {
        return 'PHPSpec spec functions MUST NOT have a public scope.';
    }

    public function getSampleCode(): string
    {
        return <<<SPEC
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{
    public function let(\$file) {
        return;
    }

    public function letGo(\$file) {
        return;
    }

    public function it_is_a_spec(\$file) {
        return;
    }

    public function itIsNotASpec(\$file) {
        return;
    }

    public function its_other_function(\$file) {
        return;
    }
}
SPEC;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return (new VisibilityRequiredFixer())->getPriority() - 1;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if ($token->getId() !== T_FUNCTION) {
                continue;
            }

            $nextIndex     = $tokens->getNextMeaningfulToken($index);
            $next          = $tokens[$nextIndex];
            $previousIndex = $tokens->getPrevMeaningfulToken($index);
            $previous      = $tokens[$previousIndex];

            if (null === $nextIndex || null === $previousIndex) {
                continue;
            }

            if ($next->getId() !== T_STRING) {
                continue;
            }

            if (0 === preg_match('/^(let(Go)?|it_.+)$/', $next->getContent())) {
                continue;
            }

            if ($previous->getId() === T_PUBLIC) {
                $tokens->overrideAt($previousIndex, '');
                $tokens->removeTrailingWhitespace($previousIndex);
            }
        }
    }
}
