<?php

namespace PedroTroller\CS\Fixer\Phpspec;

use PedroTroller\CS\Fixer\AbstractOrderedClassElementsFixer;
use PedroTroller\CS\Fixer\PhpspecFixer;
use PedroTroller\CS\Fixer\Priority;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Tokenizer\Tokens;

final class OrderedSpecElementsFixer extends AbstractOrderedClassElementsFixer
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
        return 'PHPSpec spec functions MUST BE ordered with specs first (order: let, letGo, its_* and it_* functons).';
    }

    /**
     * {@inheritdoc}
     */
    public function isDeprecated()
    {
        return true;
    }

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

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return Priority::before(OrderedClassElementsFixer::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function sortElements(array $elements)
    {
        $ordered = array_merge(
            array_values($this->filterElementsByMethodName('let', $elements)),
            array_values($this->filterElementsByMethodName('letGo', $elements)),
            array_values($this->filterElementsByMethodName('it_is_initializable', $elements)),
            array_values($this->filterElementsByMethodName('^(?!it_is_initializable)(it_|its_).+?$', $elements)),
            array_values($this->filterElementsByMethodName('getMatchers', $elements))
        );

        foreach ($this->filterElementsByType('method', $elements) as $element) {
            if (\in_array($element, $ordered, true)) {
                continue;
            }

            $ordered[] = $element;
        }

        foreach ($elements as $element) {
            if (\in_array($element, $ordered, true)) {
                continue;
            }

            array_unshift($ordered, $element);
        }

        return $ordered;
    }

    /**
     * @param string $regex
     *
     * @return array
     */
    private function filterElementsByMethodName($regex, array $elements)
    {
        $filter = [];

        foreach ($this->filterElementsByType('method', $elements) as $index => $method) {
            if (0 !== preg_match(sprintf('/^%s$/', $regex), $method['methodName'])) {
                $filter[$index] = $method;
            }
        }

        return $filter;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    private function filterElementsByType($type, array $elements)
    {
        $filter = [];

        foreach ($elements as $index => $element) {
            if ($type !== $element['type']) {
                continue;
            }

            $filter[$index] = $element;
        }

        return $filter;
    }
}
