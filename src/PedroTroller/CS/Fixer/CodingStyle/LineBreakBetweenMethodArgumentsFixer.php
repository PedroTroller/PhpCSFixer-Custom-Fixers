<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\CodingStyle;

use PedroTroller\CS\Fixer\AbstractFixer;
use PedroTroller\CS\Fixer\Priority;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class LineBreakBetweenMethodArgumentsFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    public const T_TYPEHINT_SEMI_COLON = 10025;

    public function getPriority(): int
    {
        return min(
            Priority::after(BracesFixer::class),
            Priority::after(MethodArgumentSpaceFixer::class),
        );
    }

    public function getSampleConfigurations(): array
    {
        return [
            [
                'max-args'                 => 4,
                'max-length'               => 120,
                'automatic-argument-merge' => true,
                'inline-attributes'        => true,
            ],
            [
                'max-args'                 => false,
                'max-length'               => 120,
                'automatic-argument-merge' => true,
                'inline-attributes'        => true,
            ],
        ];
    }

    public function getDocumentation(): string
    {
        return 'If the declaration of a method is too long, the arguments of this method MUST BE separated (one argument per line)';
    }

    public function getSampleCode(): string
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

    public function getConfigurationDefinition(): FixerConfigurationResolverInterface
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
            (new FixerOptionBuilder('inline-attributes', 'In the case of a split, the declaration of the attributes of the arguments of the method will be on the same line as the arguments themselves'))
                ->setDefault(false)
                ->getOption(),
        ]);
    }

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

            if ('(' !== $openBrace->getContent()) {
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

            if (false !== $this->configuration['max-args'] && $this->analyze($tokens)->getNumberOfArguments($index) > $this->configuration['max-args']) {
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
        $this->mergeArgs($tokens, $index);

        $openBraceIndex  = $tokens->getNextTokenOfKind($index, ['(']);
        $closeBraceIndex = $this->analyze($tokens)->getClosingParenthesis($openBraceIndex);

        if (0 === $closeBraceIndex) {
            return;
        }

        if ('{' === $tokens[$tokens->getNextMeaningfulToken($closeBraceIndex)]->getContent()) {
            $tokens->removeTrailingWhitespace($closeBraceIndex);
            $tokens->ensureWhitespaceAtIndex($closeBraceIndex, 1, ' ');
        }

        if ($tokens[$tokens->getNextMeaningfulToken($closeBraceIndex)]->isGivenKind(self::T_TYPEHINT_SEMI_COLON)) {
            $end = $tokens->getNextTokenOfKind($closeBraceIndex, [';', '{']);

            $tokens->removeLeadingWhitespace($end);

            if (';' !== $tokens[$end]->getContent()) {
                $tokens->ensureWhitespaceAtIndex($end, 0, ' ');
            }
        }

        $linebreaks = [$openBraceIndex, $closeBraceIndex - 1];

        for ($i = $openBraceIndex + 1; $i < $closeBraceIndex; ++$i) {
            if ('(' === $tokens[$i]->getContent()) {
                $i = $this->analyze($tokens)->getClosingParenthesis($i);
            }

            if ('[' === $tokens[$i]->getContent()) {
                $i = $this->analyze($tokens)->getClosingBracket($i);
            }

            if (',' === $tokens[$i]->getContent()) {
                $linebreaks[] = $i;
            }

            if (false === $this->configuration['inline-attributes'] && $tokens[$i]->isGivenKind(T_ATTRIBUTE)) {
                $i = $this->analyze($tokens)->getClosingAttribute($i);

                $linebreaks[] = $i;
            }
        }

        sort($linebreaks);

        foreach (array_reverse($linebreaks, false) as $iteration => $linebreak) {
            $tokens->removeTrailingWhitespace($linebreak);

            switch ($iteration) {
                case 0:
                    $whitespace = "\n".$this->analyze($tokens)->getLineIndentation($index);

                    break;

                default:
                    $whitespace = "\n".$this->analyze($tokens)->getLineIndentation($index).'    ';

                    break;
            }

            $tokens->ensureWhitespaceAtIndex($linebreak, 1, $whitespace);
        }
    }

    private function mergeArgs(Tokens $tokens, $index): void
    {
        $openBraceIndex  = $tokens->getNextTokenOfKind($index, ['(']);
        $closeBraceIndex = $this->analyze($tokens)->getClosingParenthesis($openBraceIndex);

        foreach ($tokens->findGivenKind(T_WHITESPACE, $openBraceIndex, $closeBraceIndex) as $spaceIndex => $spaceToken) {
            $tokens[$spaceIndex] = new Token([T_WHITESPACE, ' ']);
        }

        $tokens->removeTrailingWhitespace($openBraceIndex);
        $tokens->removeLeadingWhitespace($closeBraceIndex);

        $end = $tokens->getNextTokenOfKind($closeBraceIndex, [';', '{']);

        if ('{' === $tokens[$end]->getContent()) {
            $tokens->removeLeadingWhitespace($end);
            $tokens->ensureWhitespaceAtIndex($end, -1, "\n".$this->analyze($tokens)->getLineIndentation($index));
        }
    }

    private function localizeNextCloseBrace(Tokens $tokens, $index)
    {
        $opening = 0;

        for ($i = $index + 1; $i < $tokens->count(); ++$i) {
            if ('(' === $tokens[$i]->getContent()) {
                ++$opening;
            }

            if (')' === $tokens[$i]->getContent()) {
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
            if ('[' === $tokens[$i]->getContent()) {
                ++$opening;
            }

            if (']' === $tokens[$i]->getContent()) {
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

        if (')' === $tokens[$tokens->getNextMeaningfulToken($open)]->getContent()) {
            return 0;
        }

        $close     = $this->analyze($tokens)->getClosingParenthesis($open);
        $arguments = 1;

        for ($i = $open + 1; $i < $close; ++$i) {
            if ('(' === $tokens[$i]->getContent()) {
                $i = $this->analyze($tokens)->getClosingParenthesis($i);
            }

            if ('[' === $tokens[$i]->getContent()) {
                $i = $this->analyze($tokens)->getClosingBracket($i);
            }

            if (',' === $tokens[$i]->getContent()) {
                ++$arguments;
            }
        }

        return $arguments;
    }
}
