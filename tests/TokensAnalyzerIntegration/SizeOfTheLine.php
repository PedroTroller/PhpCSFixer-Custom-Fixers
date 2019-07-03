<?php

declare(strict_types=1);

namespace tests\TokensAnalyzerIntegration;

use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use tests\TokensAnalyzerIntegration;
use Webmozart\Assert\Assert;

class SizeOfTheLine extends TokensAnalyzerIntegration
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
        $this->isAVeryLongMethodCall()
            ->andThisIsAnOtherMethod()
        ;
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function assertions(TokensAnalyzer $analyzer, Tokens $tokens): void
    {
        Assert::eq(
            $analyzer->getSizeOfTheLine(
                $this->tokenContaining($tokens, 'TheClass')
            ),
            14
        );

        Assert::eq(
            $analyzer->getSizeOfTheLine(
                $this->tokenContaining($tokens, 'theFunction')
            ),
            33
        );

        Assert::eq(
            $analyzer->getSizeOfTheLine(
                $this->tokenContaining($tokens, 'isAVeryLongMethodCall')
            ),
            38
        );

        Assert::eq(
            $analyzer->getSizeOfTheLine(
                $this->tokenContaining($tokens, 'andThisIsAnOtherMethod')
            ),
            38
        );
    }
}
