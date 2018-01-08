<?php

namespace PedroTroller\CS\Fixer\Phpspec;

use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

class OrderedSpecElementsFixer extends AbstractFixer
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
        return 'PHPSpec spec functions MUST BE ordered with specs first (order: let, letGo and it_* functons).';
    }

    public function getSampleCode(): string
    {
        return <<<SPEC
<?php

namespace spec\Project\TheNamespace;

use PhpSpec\ObjectBehavior;

class TheSpec extends ObjectBehavior
{

    function letGo(\$file) {
        return;
    }

    private function thePrivateMethod() {
        return;
    }

    public function itIsNotASpec(\$file) {
        return;
    }

    function it_is_a_spec(\$file) {
        return;
    }

    function let(\$file) {
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
        return (new OrderedClassElementsFixer())->getPriority() - 1;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        for ($i = 1, $count = $tokens->count(); $i < $count; ++$i) {
            if (!$tokens[$i]->isClassy()) {
                continue;
            }

            $i        = $tokens->getNextTokenOfKind($i, ['{']);
            $elements = $this->getElements($tokens, $i);

            if (!$elements) {
                continue;
            }

            $sorted   = $this->sortElements($elements);
            $endIndex = $elements[count($elements) - 1]['end'];

            if ($sorted !== $elements) {
                $this->sortTokens($tokens, $i, $endIndex, $sorted);
            }

            $i = $endIndex;
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $startIndex
     *
     * @return array[]
     */
    private function getElements(Tokens $tokens, $startIndex)
    {
        static $elementTokenKinds = [CT::T_USE_TRAIT, T_CONST, T_VARIABLE, T_FUNCTION];

        ++$startIndex;
        $elements = [];

        while (true) {
            $element = [
                'start'      => $startIndex,
                'visibility' => 'public',
                'static'     => false,
            ];

            for ($i = $startIndex;; ++$i) {
                $token = $tokens[$i];

                if ($token->equals('}')) {
                    return $elements;
                }

                if ($token->isGivenKind(T_STATIC)) {
                    $element['static'] = true;

                    continue;
                }

                if ($token->isGivenKind([T_PROTECTED, T_PRIVATE])) {
                    $element['visibility'] = mb_strtolower($token->getContent());

                    continue;
                }

                if (!$token->isGivenKind($elementTokenKinds)) {
                    continue;
                }

                $type                  = $this->detectElementType($tokens, $i);
                $element['type']       = $type;
                $element['methodName'] = $tokens[$tokens->getNextMeaningfulToken($i)]->getContent();
                $element['end']        = $this->findElementEnd($tokens, $i);

                break;
            }

            $elements[] = $element;
            $startIndex = $element['end'] + 1;
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return array|string type or array of type and name
     */
    private function detectElementType(Tokens $tokens, $index)
    {
        $token = $tokens[$index];

        if ($token->isGivenKind(CT::T_USE_TRAIT)) {
            return 'use_trait';
        }

        if ($token->isGivenKind(T_CONST)) {
            return 'constant';
        }

        if ($token->isGivenKind(T_VARIABLE)) {
            return 'property';
        }

        $nameToken = $tokens[$tokens->getNextMeaningfulToken($index)];

        if ($nameToken->equals([T_STRING, '__construct'], false)) {
            return 'construct';
        }

        if ($nameToken->equals([T_STRING, '__destruct'], false)) {
            return 'destruct';
        }

        if (
            $nameToken->equalsAny([
                [T_STRING, 'setUpBeforeClass'],
                [T_STRING, 'tearDownAfterClass'],
                [T_STRING, 'setUp'],
                [T_STRING, 'tearDown'],
            ], false)
        ) {
            return ['phpunit', mb_strtolower($nameToken->getContent())];
        }

        if ('__' === mb_substr($nameToken->getContent(), 0, 2)) {
            return 'magic';
        }

        return 'method';
    }

    /**
     * @param Tokens $tokens
     * @param int    $index
     *
     * @return int
     */
    private function findElementEnd(Tokens $tokens, $index)
    {
        $index = $tokens->getNextTokenOfKind($index, ['{', ';']);

        if ($tokens[$index]->equals('{')) {
            $index = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        }

        for (++$index; $tokens[$index]->isWhitespace(" \t") || $tokens[$index]->isComment(); ++$index);

        --$index;

        return $tokens[$index]->isWhitespace() ? $index - 1 : $index;
    }

    /**
     * @param array[] $elements
     *
     * @return array[]
     */
    private function sortElements(array $elements)
    {
        $portions = [];

        foreach ($elements as $index => $element) {
            if ('method' !== $element['type']) {
                continue;
            }

            if ('let' === $element['methodName']) {
                $portions[0] = $element;
                unset($elements[$index]);

                continue;
            }

            if ('letGo' === $element['methodName']) {
                $portions[1] = $element;
                unset($elements[$index]);

                continue;
            }

            if ('it_is_initializable' === $element['methodName']) {
                $portions[2] = $element;
                unset($elements[$index]);

                continue;
            }

            if (0 !== preg_match('/^it_.+$/', $element['methodName'])) {
                $portions[$index + 3] = $element;
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

    /**
     * @param Tokens  $tokens
     * @param int     $startIndex
     * @param int     $endIndex
     * @param array[] $elements
     */
    private function sortTokens(
        Tokens $tokens,
        $startIndex,
        $endIndex,
        array $elements
    ) {
        $replaceTokens = [];

        foreach ($elements as $element) {
            for ($i = $element['start']; $i <= $element['end']; ++$i) {
                $replaceTokens[] = clone $tokens[$i];
            }
        }

        $tokens->overrideRange($startIndex + 1, $endIndex, $replaceTokens);
    }
}
