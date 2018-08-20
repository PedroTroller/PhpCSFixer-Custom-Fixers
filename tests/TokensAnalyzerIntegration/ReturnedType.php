<?php

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
    public function assertions(TokensAnalyzer $analyzer, Tokens $tokens)
    {
        Assert::eq(
            $analyzer->getReturnedType(
                $this->tokenContaining($tokens, 'getId') - 4
            ),
            null
        );

        Assert::eq(
            $analyzer->getReturnedType(
                $this->tokenContaining($tokens, 'getType') - 4
            ),
            'string'
        );

        Assert::eq(
            $analyzer->getReturnedType(
                $this->tokenContaining($tokens, 'getName') - 4
            ),
            ['string', null]
        );

        Assert::eq(
            $analyzer->getReturnedType(
                $this->tokenContaining($tokens, 'setName') - 4
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
