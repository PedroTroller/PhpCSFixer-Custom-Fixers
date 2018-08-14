<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\CodingStyle;

use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class ForbiddenFunctionsFixer extends AbstractFixer implements ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSampleCode()
    {
        return <<<'PHP'
<?php

class MyClass {
    public function fun()
    {
        var_dump('this is a var_dump');

        $this->dump($this);

        return var_export($this);
    }

    public function dump($data)
    {
        parent::dump($this);

        return serialize($data);
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getSampleConfigurations()
    {
        return [
            ['comment' => 'YOLO'],
            ['comment' => 'NEIN NEIN NEIN !!!', 'functions' => ['var_dump', 'var_export']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentation()
    {
        return 'Prohibited functions MUST BE commented on as prohibited';
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('functions', 'The function names to be marked how prohibited'))
                ->setDefault(['var_dump', 'dump', 'die'])
                ->getOption(),
            (new FixerOptionBuilder('comment', 'The prohibition message to put in the comment'))
                ->setDefault('@TODO remove this line')
                ->getOption(),
        ]);
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        $calls = [];

        foreach ($tokens as $index => $token) {
            if (T_STRING === $token->getId()) {
                $calls[$index] = $token;
            }
        }

        foreach (array_reverse($calls, true) as $index => $token) {
            if (false === $tokens[$tokens->getNextMeaningfulToken($index)]->equals('(')) {
                continue;
            }

            if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind([T_FUNCTION, T_DOUBLE_COLON, T_OBJECT_OPERATOR])) {
                continue;
            }

            if (\in_array($token->getContent(), $this->configuration['functions'], true)) {
                $end          = $this->analyze($tokens)->getEndOfTheLine($index);
                $tokens[$end] = new Token([T_WHITESPACE, sprintf(' // %s%s', $this->configuration['comment'], $tokens[$end]->getContent())]);
            }
        }
    }
}
