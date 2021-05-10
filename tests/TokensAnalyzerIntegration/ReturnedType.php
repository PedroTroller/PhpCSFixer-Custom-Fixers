<?php

declare(strict_types=1);

namespace tests\TokensAnalyzerIntegration;

use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use tests\TokensAnalyzerIntegration;
use Webmozart\Assert\Assert;

final class ReturnedType extends TokensAnalyzerIntegration
{
    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function assertions(TokensAnalyzer $analyzer, Tokens $tokens): void
    {
        Assert::eq(
            $analyzer->getReturnedType(
                $this->tokenContaining($tokens, 'getId') - 2
            ),
            null
        );

        Assert::eq(
            $analyzer->getReturnedType(
                $this->tokenContaining($tokens, 'getType') - 2
            ),
            'string'
        );

        Assert::eq(
            $analyzer->getReturnedType(
                $this->tokenContaining($tokens, 'getName') - 2
            ),
            ['string', null]
        );

        Assert::eq(
            $analyzer->getReturnedType(
                $this->tokenContaining($tokens, 'setName') - 2
            ),
            'void'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 70100;
    }
}
