<?php

declare(strict_types=1);

namespace tests\TokensAnalyzerIntegration;

use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use tests\TokensAnalyzerIntegration;
use Webmozart\Assert\Assert;

class InSwitch extends TokensAnalyzerIntegration
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
    public function theFunction()
    {
        switch ('foo') {
            case true:
                return 'bar';
            case false:
                return 'baz';
        }
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function assertions(TokensAnalyzer $analyzer, Tokens $tokens): void
    {
        Assert::false(
            $analyzer->isInsideSwitchCase(
                $this->tokenContaining($tokens, "'foo'")
            )
        );

        Assert::true(
            $analyzer->isInsideSwitchCase(
                $this->tokenContaining($tokens, "'bar'")
            )
        );

        Assert::true(
            $analyzer->isInsideSwitchCase(
                $this->tokenContaining($tokens, "'baz'")
            )
        );
    }
}
