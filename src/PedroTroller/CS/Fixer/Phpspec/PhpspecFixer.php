<?php

namespace PedroTroller\CS\Fixer\Phpspec;

use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class PhpspecFixer extends AbstractFixer
{
    // @var AbstractFixer[]
    private $fixers;

    public function __construct()
    {
        parent::__construct();

        $this->fixers = [
            new OrderedSpecElementsFixer(),
            new PhpspecScenarioReturnTypeDeclarationFixer(),
            new PhpspecScenarioScopeFixer(),
        ];
    }

    // {@inheritdoc}
    public function isCandidate(Tokens $tokens)
    {
        foreach ($this->fixers as $fixer) {
            if (false === $fixer->isCandidate($tokens)) {
                return false;
            }
        }

        return true;
    }

    // {@inheritdoc}
    public function getDocumentation()
    {
        return implode(
            "\n",
            array_map(
                function (AbstractFixer $fixer) {
                    return ' - '.trim($fixer->getDocumentation(), ' .');
                },
                $this->fixers
            )
        ).'.';
    }

    // {@inheritdoc}
    public function getSampleCode()
    {
        return <<<'SPEC'
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{

    public function letGo($file) {
        return;
    }

    private function thePrivateMethod() {
        return;
    }

    public function itIsNotASpec($file) {
        return;
    }

    public function it_is_a_spec($file) {
        return;
    }

    public function let($file) {
        return;
    }

    public function its_other_function($file) {
        return;
    }
}
SPEC;
    }

    // {@inheritdoc}
    public function getPriority()
    {
        return min(
            array_map(
                function (AbstractFixer $fixer) {
                    return $fixer->getPriority();
                },
                $this->fixers
            )
        ) - 1;
    }

    // {@inheritdoc}
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        foreach ($this->fixers as $fixer) {
            if ($fixer->isCandidate($tokens)) {
                $fixer->applyFix($file, $tokens);
            }
        }
    }
}
