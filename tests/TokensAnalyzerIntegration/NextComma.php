<?php

declare(strict_types=1);

namespace tests\TokensAnalyzerIntegration;

use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use tests\TokensAnalyzerIntegration;
use Webmozart\Assert\Assert;

final class NextComma extends TokensAnalyzerIntegration
{
    public function getCode()
    {
        return <<<'PHP'
            <?php

            namespace TheNamespace;

            class TheClass
            {
                public function __construct(
                    public string $theString = 'N/A',
                    public array $theArray = [1, 2, 3, 4]
                ) {
                }
            }
            PHP;
    }

    public function assertions(TokensAnalyzer $analyzer, Tokens $tokens): void
    {
        Assert::eq(
            $analyzer->getNextComma(
                $this->tokenContaining($tokens, '$theString')
            ),
            $this->tokenContaining($tokens, '$theString') + 5,
        );

        Assert::eq(
            $analyzer->getNextComma(
                $this->tokenContaining($tokens, '$theArray')
            ),
            null,
        );
    }
}
