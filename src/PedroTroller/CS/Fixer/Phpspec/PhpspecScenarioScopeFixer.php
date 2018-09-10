<?php

namespace PedroTroller\CS\Fixer\Phpspec;

use PedroTroller\CS\Fixer\AbstractFixer;
use PedroTroller\CS\Fixer\PhpspecFixer;
use PedroTroller\CS\Fixer\Priority;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class PhpspecScenarioScopeFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $this->extendsClass($tokens, 'PhpSpec\ObjectBehavior');
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentation()
    {
        return 'PHPSpec spec functions MUST NOT have a public scope.';
    }

    /**
     * {@inheritdoc}
     */
    public function isDeprecated()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeprecationReplacement()
    {
        return (new PhpspecFixer())->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getSampleCode()
    {
        return <<<'SPEC'
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{
    public function let($file) {
        return;
    }

    public function letGo($file) {
        return;
    }

    public function it_is_a_spec($file) {
        return;
    }

    public function itIsNotASpec($file) {
        return;
    }

    public function its_other_function($file) {
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
        return Priority::after(VisibilityRequiredFixer::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (T_FUNCTION !== $token->getId()) {
                continue;
            }

            $nextIndex     = $tokens->getNextMeaningfulToken($index);
            $next          = $tokens[$nextIndex];
            $previousIndex = $tokens->getPrevMeaningfulToken($index);
            $previous      = $tokens[$previousIndex];

            if (null === $nextIndex || null === $previousIndex) {
                continue;
            }

            if (T_STRING !== $next->getId()) {
                continue;
            }

            if (0 === preg_match('/^(let(Go)?|it_.+|its_.+)$/', $next->getContent())) {
                continue;
            }

            if (T_PUBLIC === $previous->getId()) {
                $tokens[$previousIndex] = new Token('');
                $tokens->removeTrailingWhitespace($previousIndex);
            }
        }
    }
}
