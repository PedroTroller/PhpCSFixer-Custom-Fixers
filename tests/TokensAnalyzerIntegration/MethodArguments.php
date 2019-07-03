<?php

declare(strict_types=1);

namespace tests\TokensAnalyzerIntegration;

use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use tests\TokensAnalyzerIntegration;
use Webmozart\Assert\Assert;

class MethodArguments extends TokensAnalyzerIntegration
{
    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return <<<'PHP'
<?php

namespace Project\TheNamespace;

class TheClass
{
    public function __construct()
    {
    }

    public function theFunction(
        Domain\Model\User $user,
        $boolean = true
    ) {
        return $user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function assertions(TokensAnalyzer $analyzer, Tokens $tokens): void
    {
        $methods = array_filter(
            $analyzer->getClassyElements(),
            function ($element) { return 'method' === $element['type']; }
        );

        Assert::count($methods, 3);

        [$contructor, $theFunction, $setUser] = array_keys($methods);

        $arguments = $analyzer->getMethodArguments($contructor);
        Assert::eq($analyzer->getNumberOfArguments($contructor), 0);
        Assert::count($arguments, 0);

        $arguments = $analyzer->getMethodArguments($theFunction);
        Assert::eq($analyzer->getNumberOfArguments($theFunction), 2);
        Assert::count($arguments, 2);
        Assert::eq(
            $arguments,
            [
                ($theFunction + 5) => [
                    'type'      => 'Domain\\Model\\User',
                    'name'      => '$user',
                    'nullable'  => false,
                    'asDefault' => false,
                ],
                ($theFunction + 14) => [
                    'type'      => null,
                    'name'      => '$boolean',
                    'nullable'  => false,
                    'asDefault' => true,
                ],
            ]
        );

        $arguments = $analyzer->getMethodArguments($setUser);
        Assert::eq($analyzer->getNumberOfArguments($setUser), 1);
        Assert::count($arguments, 1);
        Assert::eq(
            $arguments,
            [
                ($setUser + 4) => [
                    'type'      => null,
                    'name'      => '$user',
                    'nullable'  => false,
                    'asDefault' => false,
                ],
            ]
        );
    }
}
