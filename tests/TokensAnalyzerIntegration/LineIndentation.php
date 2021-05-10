<?php

declare(strict_types=1);

namespace tests\TokensAnalyzerIntegration;

use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use tests\TokensAnalyzerIntegration;
use Webmozart\Assert\Assert;

final class LineIndentation extends TokensAnalyzerIntegration
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
        Assert::eq(
            $analyzer->getLineIndentation(
                $this->tokenContaining($tokens, 'theFunction')
            ),
            '    '
        );

        Assert::eq(
            $analyzer->getLineIndentation(
                $this->tokenContaining($tokens, "'foo'")
            ),
            '        '
        );

        Assert::eq(
            $analyzer->getLineIndentation(
                $this->tokenContaining($tokens, "'baz'")
            ),
            '                '
        );
    }
}
