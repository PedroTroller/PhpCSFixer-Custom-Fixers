<?php

declare(strict_types=1);

namespace tests\TokensAnalyzerIntegration;

use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use tests\TokensAnalyzerIntegration;
use Webmozart\Assert\Assert;

final class Parenthesis extends TokensAnalyzerIntegration
{
    public function getCode()
    {
        return <<<'PHP'
            <?php

            class MyClass {

                /**
                 * @var string|null
                 */
                private $name;

                public function getId()
                {
                }

                public function getType(): string
                {
                    return 'class';
                }

                public function getName(): ?string
                {
                    return $this->name;
                }

                public function setName(string $name): void
                {
                    $this->name = $name;
                }
            }
            PHP;
    }

    public function assertions(TokensAnalyzer $analyzer, Tokens $tokens): void
    {
        Assert::eq(
            $analyzer->getClosingParenthesis($this->tokenContaining($tokens, 'getId') + 1),
            $this->tokenContaining($tokens, 'getId') + 2
        );

        Assert::eq(
            $analyzer->getClosingParenthesis($this->tokenContaining($tokens, 'getType') + 1),
            $this->tokenContaining($tokens, 'getType') + 2
        );

        Assert::eq(
            $analyzer->getClosingParenthesis($this->tokenContaining($tokens, 'getName') + 1),
            $this->tokenContaining($tokens, 'getName') + 2
        );

        Assert::eq(
            $analyzer->getClosingParenthesis($this->tokenContaining($tokens, 'setName') + 1),
            $this->tokenContaining($tokens, 'setName') + 5
        );
    }

    /**
     * @return int
     */
    public function getMinSupportedPhpVersion()
    {
        return 70100;
    }
}
