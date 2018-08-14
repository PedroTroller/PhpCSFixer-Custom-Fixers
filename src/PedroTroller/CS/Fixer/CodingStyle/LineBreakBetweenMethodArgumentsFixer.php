<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\CodingStyle;

use PedroTroller\CS\Fixer\AbstractFixer;
use PedroTroller\CS\Fixer\Priority;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class LineBreakBetweenMethodArgumentsFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    public const T_TYPEHINT_SEMI_COLON = 10025;

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return Priority::after(BracesFixer::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getSampleConfigurations()
    {
        return [
            [
                'max-args'                 => 4,
                'max-length'               => 120,
                'automatic-argument-merge' => true,
            ],
            [
                'max-args'                 => false,
                'max-length'               => 120,
                'automatic-argument-merge' => true,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentation()
    {
        return 'If the declaration of a method is too long, the arguments of this method MUST BE separated (one argument per line)';
    }

    /**
     * {@inheritdoc}
     */
    public function getSampleCode()
    {
        return <<<'SPEC'
<?php

namespace Project\TheNamespace;

class TheClass
{
    public function fun1($arg1, array $arg2 = [], $arg3 = null)
    {
        return;
    }

    public function fun2($arg1, array $arg2 = [], \ArrayAccess $arg3 = null, bool $bool = true, \Iterator $thisLastArgument = null)
    {
        return;
    }

    public function fun3(
        $arg1,
        array $arg2 = []
    ) {
        return;
    }
}
SPEC;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('max-args', 'The maximum number of arguments allowed with splitting the arguments into several lines (use `false` to disable this feature)'))
                ->setDefault(3)
                ->getOption(),
            (new FixerOptionBuilder('max-length', 'The maximum number of characters allowed with splitting the arguments into several lines'))
                ->setDefault(120)
                ->getOption(),
            (new FixerOptionBuilder('automatic-argument-merge', 'If both conditions are met (the line is not too long and there are not too many arguments), then the arguments are put back inline'))
                ->setDefault(true)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        $functions = [];

        foreach ($tokens as $index => $token) {
            if (T_FUNCTION === $token->getId()) {
                $functions[$index] = $token;
            }
        }

        foreach (array_reverse($functions, true) as $index => $token) {
            $nextIndex = $tokens->getNextMeaningfulToken($index);
            $next      = $tokens[$nextIndex];

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

            if (0 === $this->analyze($tokens)->getNumberOfArguments($index)) {
                $this->mergeArgs($tokens, $index);

                continue;
            }

            if ($this->analyze($tokens)->getSizeOfTheLine($index) > $this->configuration['max-length']) {
                $this->splitArgs($tokens, $index);

                continue;
            }

            if ($this->analyze($tokens)->getNumberOfArguments($index) > $this->configuration['max-args']) {
                $this->splitArgs($tokens, $index);

                continue;
            }

            $clonedTokens = clone $tokens;
            $this->mergeArgs($clonedTokens, $index);

            if ($this->analyze($clonedTokens)->getSizeOfTheLine($index) > $this->configuration['max-length']) {
                $this->splitArgs($tokens, $index);
            } elseif ($this->configuration['automatic-argument-merge']) {
                $this->mergeArgs($tokens, $index);
            }
        }
    }

    private function splitArgs(Tokens $tokens, $index): void
    {
        $openBraceIndex  = $tokens->getNextTokenOfKind($index, ['(']);
        $closeBraceIndex = $this->analyze($tokens)->getClosingParenthesis($openBraceIndex);

        if (0 === $closeBraceIndex) {
            return;
        }

        $token                   = $tokens[$openBraceIndex];
        $tokens[$openBraceIndex] = new Token([
            T_WHITESPACE,
            trim($token->getContent())."\n".$this->analyze($tokens)->getLineIndentation($index).$this->whitespacesConfig->getIndent(),
        ]);

        $token                    = $tokens[$closeBraceIndex];
        $tokens[$closeBraceIndex] = new Token([
            T_WHITESPACE,
            rtrim($this->whitespacesConfig->getLineEnding().$this->analyze($tokens)->getLineIndentation($index).$token->getContent()),
        ]);

        if ($tokens[$tokens->getNextMeaningfulToken($closeBraceIndex)]->equals('{')) {
            $tokens->removeTrailingWhitespace($closeBraceIndex);
            $token                                                     = $tokens[$tokens->getNextMeaningfulToken($closeBraceIndex)];
            $tokens[$tokens->getNextMeaningfulToken($closeBraceIndex)] = new Token([
                T_WHITESPACE,
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
                $i = $this->analyze($tokens)->getClosingParenthesis($i);
            }

            if ($tokens[$i]->equals('[')) {
                $i = $this->analyze($tokens)->getClosingBracket($i);
            }

            if ($tokens[$i]->equals(',')) {
                $tokens->removeTrailingWhitespace($i);
                $token      = $tokens[$i];
                $tokens[$i] = new Token([
                    T_WHITESPACE,
                    trim($token->getContent())."\n".$this->analyze($tokens)->getLineIndentation($index).$this->whitespacesConfig->getIndent(),
                ]);
            }
        }

        $tokens->removeTrailingWhitespace($openBraceIndex);
        $tokens->removeTrailingWhitespace($tokens->getPrevMeaningfulToken($closeBraceIndex));
    }

    private function mergeArgs(Tokens $tokens, $index): void
    {
        $openBraceIndex  = $tokens->getNextTokenOfKind($index, ['(']);
        $closeBraceIndex = $this->analyze($tokens)->getClosingParenthesis($openBraceIndex);

        for ($i = $openBraceIndex; $i <= $closeBraceIndex; ++$i) {
            $content    = preg_replace('/ {2,}/', ' ', str_replace("\n", '', $tokens[$i]->getContent()));
            $tokens[$i] = $tokens[$i]->getId()
                ? new Token([$tokens[$i]->getId(), $content])
                : new Token([T_WHITESPACE, $content]);
        }

        $tokens->removeTrailingWhitespace($openBraceIndex);
        $tokens->removeLeadingWhitespace($closeBraceIndex);

        $end = $tokens->getNextTokenOfKind($closeBraceIndex, [';', '{']);

        if ($tokens[$end]->equals('{')) {
            $tokens->removeLeadingWhitespace($end);
            $tokens->insertAt($end, new Token([T_WHITESPACE, "\n".$this->analyze($tokens)->getLineIndentation($index)]));
        }
    }

    private function localizeNextCloseBrace(Tokens $tokens, $index)
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

    private function localizeNextCloseBracket(Tokens $tokens, $index)
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

    private function getNumberOfArguments(Tokens $tokens, $index)
    {
        if (T_FUNCTION !== $tokens[$index]->getId()) {
            return 0;
        }

        $open = $tokens->getNextTokenOfKind($index, ['(']);

        if ($tokens[$tokens->getNextMeaningfulToken($open)]->equals(')')) {
            return 0;
        }

        $close     = $this->analyze($tokens)->getClosingParenthesis($open);
        $arguments = 1;

        for ($i = $open + 1; $i < $close; ++$i) {
            if ($tokens[$i]->equals('(')) {
                $i = $this->analyze($tokens)->getClosingParenthesis($i);
            }

            if ($tokens[$i]->equals('[')) {
                $i = $this->analyze($tokens)->getClosingBracket($i);
            }

            if ($tokens[$i]->equals(',')) {
                ++$arguments;
            }
        }

        return $arguments;
    }
}
