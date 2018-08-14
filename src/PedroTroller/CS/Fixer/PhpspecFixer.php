<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class PhpspecFixer extends AbstractOrderedClassElementsFixer implements ConfigurableFixerInterface
{
    public function getSampleConfigurations()
    {
        return [
            [],
            ['instanceof' => ['PhpSpec\ObjectBehavior']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        foreach ($this->configuration['instanceof'] as $parent) {
            if ($this->extendsClass($tokens, $parent)) {
                return true;
            }

            if ($this->implementsInterface($tokens, $parent)) {
                return true;
            }
        }

        return false;
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

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return Priority::after(VisibilityRequiredFixer::class);
    }

    public function getDocumentation()
    {
        return implode(
            "\n\n",
            [
                'Phpspec scenario functions MUST NOT have a return type declaration.',
                'Phpspec scenario functions MUST NOT have a scope.',
                'The methods of the phpspec specification classes MUST BE sorted (let, letGo, its_*, it_*, getMatchers and the rest of the methods)',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('instanceof', 'Parent classes of your spec classes.'))
                ->setDefault(['PhpSpec\ObjectBehavior'])
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function sortElements(array $elements)
    {
        $ordered = array_merge(
            array_values($this->filterElementsByType('construct', $elements)),
            array_values($this->filterElementsByMethodName('let', $elements)),
            array_values($this->filterElementsByMethodName('letGo', $elements)),
            array_values($this->filterElementsByMethodName('it_is_initializable', $elements)),
            array_values($this->filterElementsByMethodName('^(?!it_is_initializable$)(it_|its_).+?$', $elements)),
            array_values($this->filterElementsByMethodName('getMatchers', $elements))
        );

        foreach ($this->filterElementsByType('method', $elements) as $element) {
            if (\in_array($element, $ordered, true)) {
                continue;
            }

            $ordered[] = $element;
        }

        foreach (array_reverse($elements) as $element) {
            if (\in_array($element, $ordered, true)) {
                continue;
            }

            array_unshift($ordered, $element);
        }

        return $ordered;
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        $this->removeScope($file, $tokens);
        $this->removeReturn($file, $tokens);

        parent::applyFix($file, $tokens);
    }

    private function removeScope(SplFileInfo $file, Tokens $tokens): void
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

    private function removeReturn(SplFileInfo $file, Tokens $tokens): void
    {
        foreach ($tokens as $index => $token) {
            if (T_FUNCTION !== $token->getId()) {
                continue;
            }

            $functionNameIndex = $tokens->getNextMeaningfulToken($index);

            if (null === $functionNameIndex) {
                continue;
            }

            $functionName = $tokens[$functionNameIndex];

            if (T_STRING !== $functionName->getId()) {
                continue;
            }

            if (0 === preg_match('/^(let(Go)?|it_.+|its_.+)$/', $functionName->getContent())) {
                continue;
            }

            $openBraceIndex    = $tokens->getNextTokenOfKind($index, ['(']);
            $closeBraceIndex   = $this->analyze($tokens)->getClosingParenthesis($openBraceIndex);
            $openCurlyBracket  = $tokens->getNextTokenOfKind($index, ['{']);
            $returnDeclaration = $this->analyze($tokens)->getReturnedType($index);

            if (null === $returnDeclaration || null === $closeBraceIndex || null === $openCurlyBracket) {
                continue;
            }

            if ($closeBraceIndex >= $openCurlyBracket - 1) {
                continue;
            }

            $tokens->clearRange($closeBraceIndex + 1, $openCurlyBracket - 1);
            $tokens->insertAt($openCurlyBracket, new Token(' '));
        }
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
