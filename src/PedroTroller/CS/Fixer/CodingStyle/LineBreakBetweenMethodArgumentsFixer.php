<?php

namespace PedroTroller\CS\Fixer\CodingStyle;

use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

class LineBreakBetweenMethodArgumentsFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface, WhitespacesAwareFixerInterface
{
    const T_TYPEHINT_SEMI_COLON = 10025;

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return (new BracesFixer())->getPriority() - 1;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    public function getSampleConfigurations(): array
    {
        return [
            ['max-args' => 4, 'max-length' => 80],
        ];
    }

    public function getDocumentation(): string
    {
        return 'Function methods MUST be splitted by a line break';
    }

    public function getSampleCode(): string
    {
        return <<<SPEC
<?php

namespace Project\TheNamespace;

class TheClass
{
    public function fun1(\$arg1, array \$arg2 = [], \$arg3 = null) {
        return;
    }

    public function fun2(\$arg1, array \$arg2 = [], \ArrayAccess \$arg3 = null, bool \$bool = true) {
        return;
    }

    public function fun3(
        \$arg1,
        array \$arg2 = []
    ) {
        return;
    }
}
SPEC;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (T_FUNCTION !== $token->getId()) {
                continue;
            }

            $nextIndex     = $tokens->getNextMeaningfulToken($index);
            $next          = $tokens[$nextIndex];

            if (null === $nextIndex) {
                continue;
            }

            if (T_STRING !== $next->getId()) {
                continue;
            }

            $openBraceIndex = $tokens->getNextMeaningfulToken($nextIndex);
            $openBrace      = $tokens[$openBraceIndex];

            if (false === $openBrace->equals('(')) {
                continue;
            }

            if ($this->getLineSize($tokens, $index) > $this->configuration['max-length']) {
                $this->splitArgs($tokens, $index);

                continue;
            }

            if ($this->getNumberOfArguments($tokens, $index) > $this->configuration['max-args']) {
                $this->splitArgs($tokens, $index);

                continue;
            }

            $this->mergeArgs($tokens, $index);

            if ($this->getLineSize($tokens, $index) > $this->configuration['max-length']) {
                $this->splitArgs($tokens, $index);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('max-args', 'Then maximum number of arguments authorized in a same function definition'))
                ->setDefault(3)
                ->getOption(),
            (new FixerOptionBuilder('max-length', 'Then maximum line size authorized'))
                ->setDefault(180)
                ->getOption(),
        ]);
    }

    private function splitArgs(Tokens $tokens, int $index)
    {
        $openBraceIndex  = $tokens->getNextTokenOfKind($index, ['(']);
        $closeBraceIndex = $this->localizeNextCloseBrace($tokens, $index);

        if (0 === $closeBraceIndex) {
            return;
        }

        $token                   = $tokens[$openBraceIndex];
        $tokens[$openBraceIndex] = new Token([
            $token->getId(),
            rtrim($token->getContent()).$this->whitespacesConfig->getLineEnding().$this->whitespacesConfig->getIndent().$this->whitespacesConfig->getIndent(),
        ]);

        $token                    = $tokens[$closeBraceIndex];
        $tokens[$closeBraceIndex] = new Token([
            $token->getId(),
            rtrim($this->whitespacesConfig->getLineEnding().$this->whitespacesConfig->getIndent().$token->getContent()),
        ]);

        if ($tokens[$tokens->getNextMeaningfulToken($closeBraceIndex)]->equals('{')) {
            $tokens->removeTrailingWhitespace($closeBraceIndex);
            $token                                                     = $tokens[$tokens->getNextMeaningfulToken($closeBraceIndex)];
            $tokens[$tokens->getNextMeaningfulToken($closeBraceIndex)] = new Token([
                $token->getId(),
                ' '.trim($token->getContent()),
            ]);
        }

        if ($tokens[$tokens->getNextMeaningfulToken($closeBraceIndex)]->isGivenKind(self::T_TYPEHINT_SEMI_COLON)) {
            $end = $tokens->getNextTokenOfKind($closeBraceIndex, [';', '{']);

            for ($i = $closeBraceIndex + 1; $i < $end; ++$i) {
                $content    = preg_replace('/ {2,}/', ' ', str_replace("\n", '', $tokens[$i]->getContent()));
                $tokens[$i] = new Token([$tokens[$i]->getId(), $content]);
            }
        }

        for ($i = $openBraceIndex + 1; $i < $closeBraceIndex; ++$i) {
            if ($tokens[$i]->equals('(')) {
                $i = $this->localizeNextCloseBrace($tokens, $i);
            }

            if ($tokens[$i]->equals('[')) {
                $i = $this->localizeNextCloseBracket($tokens, $i);
            }

            if ($tokens[$i]->equals(',')) {
                $tokens->removeTrailingWhitespace($i);
                $token      = $tokens[$i];
                $tokens[$i] = new Token([
                    $token->getId(),
                    trim($token->getContent())."\n".$this->whitespacesConfig->getIndent().$this->whitespacesConfig->getIndent(),
                ]);
            }
        }

        $tokens->removeTrailingWhitespace($openBraceIndex);
        $tokens->removeTrailingWhitespace($tokens->getPrevMeaningfulToken($closeBraceIndex));
    }

    private function mergeArgs(Tokens $tokens, int $index)
    {
        // Not implemented yet.

        return;
    }

    private function localizeNextCloseBrace(Tokens $tokens, $index): int
    {
        $opening = 0;

        for ($i = $index + 1; $i < $tokens->count(); ++$i) {
            if ($tokens[$i]->equals('(')) {
                ++$opening;
            }

            if ($tokens[$i]->equals(')')) {
                if ($opening > 0) {
                    --$opening;
                }

                return $i;
            }
        }

        return 0;
    }

    private function localizeNextCloseBracket(Tokens $tokens, $index): int
    {
        $opening = 0;

        for ($i = $index + 1; $i < $tokens->count(); ++$i) {
            if ($tokens[$i]->equals('[')) {
                ++$opening;
            }

            if ($tokens[$i]->equals(']')) {
                if ($opening > 0) {
                    --$opening;
                }

                return $i;
            }
        }

        return 0;
    }

    private function getNumberOfArguments(Tokens $tokens, int $index): int
    {
        if (T_FUNCTION !== $tokens[$index]->getId()) {
            return 0;
        }

        $open = $tokens->getNextTokenOfKind($index, ['(']);

        if ($tokens[$tokens->getNextMeaningfulToken($open)]->equals(')')) {
            return 0;
        }

        $close     = $this->localizeNextCloseBrace($tokens, $open);
        $arguments = 1;

        for ($i = $open + 1; $i < $close; ++$i) {
            if ($tokens[$i]->equals('(')) {
                $i = $this->localizeNextCloseBrace($tokens, $i);
            }

            if ($tokens[$i]->equals('[')) {
                $i = $this->localizeNextCloseBracket($tokens, $i);
            }

            if ($tokens[$i]->equals(',')) {
                ++$arguments;
            }
        }

        return $arguments;
    }
}
