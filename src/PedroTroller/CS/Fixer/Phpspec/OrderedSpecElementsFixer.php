<?php

namespace PedroTroller\CS\Fixer\Phpspec;

use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverRootless;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerConfiguration\FixerOptionValidatorGenerator;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

class OrderedSpecElementsFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * @var array Array containing all class element base types (keys) and their parent types (values)
     */
    private static $typeHierarchy = [
        'use_trait'                 => null,
        'public'                    => null,
        'protected'                 => null,
        'private'                   => null,
        'constant'                  => null,
        'constant_public'           => ['constant', 'public'],
        'constant_protected'        => ['constant', 'protected'],
        'constant_private'          => ['constant', 'private'],
        'property'                  => null,
        'property_static'           => ['property'],
        'property_public'           => ['property', 'public'],
        'property_protected'        => ['property', 'protected'],
        'property_private'          => ['property', 'private'],
        'property_public_static'    => ['property_static', 'property_public'],
        'property_protected_static' => ['property_static', 'property_protected'],
        'property_private_static'   => ['property_static', 'property_private'],
        'method'                    => null,
        'method_static'             => ['method'],
        'method_public'             => ['method', 'public'],
        'method_protected'          => ['method', 'protected'],
        'method_private'            => ['method', 'private'],
        'method_public_static'      => ['method_static', 'method_public'],
        'method_protected_static'   => ['method_static', 'method_protected'],
        'method_private_static'     => ['method_static', 'method_private'],
    ];

    /**
     * @var array Array containing special method types
     */
    private static $specialTypes = [
        'construct' => null,
        'destruct'  => null,
        'magic'     => null,
        'phpunit'   => null,
    ];

    /**
     * @var array Resolved configuration array (type => position)
     */
    private $typePosition;

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $this->extendsClass($tokens, 'PhpSpec\ObjectBehavior');
    }

    public function getDocumentation(): string
    {
        return 'PHPSpec spec functions MUST BE ordered with specs first.';
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
    public function configure(array $configuration = null)
    {
        parent::configure($configuration);

        $this->typePosition = [];
        $pos                = 0;

        foreach ($this->configuration['order'] as $type) {
            $this->typePosition[$type] = $pos++;
        }

        foreach (self::$typeHierarchy as $type => $parents) {
            if (isset($this->typePosition[$type])) {
                continue;
            }

            if (!$parents) {
                $this->typePosition[$type] = null;

                continue;
            }

            foreach ($parents as $parent) {
                if (isset($this->typePosition[$parent])) {
                    $this->typePosition[$type] = $this->typePosition[$parent];

                    continue 2;
                }
            }

            $this->typePosition[$type] = null;
        }

        $lastPosition = count($this->configuration['order']);

        foreach ($this->typePosition as &$pos) {
            if (null === $pos) {
                $pos = $lastPosition;
            }

            $pos *= 10;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, Tokens $tokens)
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
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolverRootless('order', [
            (new FixerOptionBuilder('order', 'List of strings defining order of elements.'))
                ->setAllowedTypes(['array'])
                ->setAllowedValues([
                    (new FixerOptionValidatorGenerator())->allowedValueIsSubsetOf(array_keys(array_merge(self::$typeHierarchy, self::$specialTypes))),
                ])
                ->setDefault([
                    'use_trait',
                    'constant_public',
                    'constant_protected',
                    'constant_private',
                    'property_public',
                    'property_protected',
                    'property_private',
                    'construct',
                    'destruct',
                    'magic',
                    'phpunit',
                    'method_public',
                    'method_protected',
                    'method_private',
                ])
                ->getOption(),
        ]);
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

                // class end
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

                $type = $this->detectElementType($tokens, $i);
                if (is_array($type)) {
                    $element['type'] = $type[0];
                    $element['name'] = $type[1];
                } else {
                    $element['type'] = $type;
                }

                $element['methodName'] = $tokens[$tokens->getNextMeaningfulToken($i)]->getContent();

                $element['end'] = $this->findElementEnd($tokens, $i);

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
        $let        = null;
        $letGo      = null;
        $initialise = null;
        $specs      = [];

        foreach ($elements as $index => $element) {
            if ('method' !== $element['type']) {
                continue;
            }

            if ('let' === $element['methodName']) {
                $let = $element;

                unset($elements[$index]);

                continue;
            }

            if ('letGo' === $element['methodName']) {
                $letGo = $element;

                unset($elements[$index]);

                continue;
            }

            if ('it_is_initializable' === $element['methodName']) {
                $initialise = $element;

                unset($elements[$index]);

                continue;
            }

            if (0 !== preg_match('/^it_.+$/', $element['methodName'])) {
                $specs[] = $element;

                unset($elements[$index]);

                continue;
            }
        }

        $sorted   = [];
        $injected = false;

        foreach ($elements as $element) {
            if ('method' !== $element['type']) {
                $sorted[] = $element;

                continue;
            }

            if (false === $injected) {
                if (null !== $let) {
                    $sorted[] = $let;

                    $let = null;
                }

                if (null !== $letGo) {
                    $sorted[] = $letGo;

                    $letGo = null;
                }

                if (null !== $initialise) {
                    $sorted[] = $initialise;

                    $initialise = null;
                }

                foreach ($specs as $spec) {
                    $sorted[] = $spec;
                }

                $specs = [];

                $injected = true;
            }

            $sorted[] = $element;
        }

        if (null !== $let) {
            $sorted[] = $let;
        }

        if (null !== $letGo) {
            $sorted[] = $letGo;
        }

        if (null !== $initialise) {
            $sorted[] = $initialise;
        }

        foreach ($specs as $spec) {
            $sorted[] = $spec;
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
