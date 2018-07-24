<?php

namespace PedroTroller\CS\Fixer\Phpspec;

use PedroTroller\CS\Fixer\AbstractOrderedClassElementsFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Tokenizer\Tokens;

final class OrderedSpecElementsFixer extends AbstractOrderedClassElementsFixer
{
    // {@inheritdoc}
    public function isCandidate(Tokens $tokens)
    {
        return $this->extendsClass($tokens, 'PhpSpec\ObjectBehavior');
    }

    // {@inheritdoc}
    public function getDocumentation()
    {
        return 'PHPSpec spec functions MUST BE ordered with specs first (order: let, letGo, its_* and it_* functons).';
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

    function letGo($file) {
        return;
    }

    private function thePrivateMethod() {
        return;
    }

    public function itIsNotASpec($file) {
        return;
    }

    function it_is_a_spec($file) {
        return;
    }

    function let($file) {
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
        return (new OrderedClassElementsFixer())->getPriority() + 1;
    }

    // {@inheritdoc}
    protected function sortElements(array $elements)
    {
        $portions         = [];
        $numberOfElements = count($elements);

        foreach ($elements as $index => $element) {
            if ('method' !== $element['type']) {
                continue;
            }

            if ('let' === $element['methodName']) {
                $portions[-4] = $element;
                unset($elements[$index]);

                continue;
            }

            if ('letGo' === $element['methodName']) {
                $portions[-3] = $element;
                unset($elements[$index]);

                continue;
            }

            if ('it_is_initializable' === $element['methodName']) {
                $portions[-1] = $element;
                unset($elements[$index]);

                continue;
            }

            if (0 !== preg_match('/^(it_|its_).+$/', $element['methodName'])) {
                $portions[$index] = $element;
                unset($elements[$index]);

                continue;
            }

            if ('getMatchers' === $element['methodName']) {
                $portions[$numberOfElements] = $element;
                unset($elements[$index]);

                continue;
            }
        }

        ksort($portions);

        $sorted = [];

        foreach ($elements as $element) {
            if ('method' !== $element['type']) {
                $sorted[] = $element;

                continue;
            }

            foreach ($portions as $portion) {
                $sorted[] = $portion;
            }

            $portions = [];

            $sorted[] = $element;
        }

        foreach ($portions as $portion) {
            $sorted[] = $portion;
        }

        return $sorted;
    }
}
