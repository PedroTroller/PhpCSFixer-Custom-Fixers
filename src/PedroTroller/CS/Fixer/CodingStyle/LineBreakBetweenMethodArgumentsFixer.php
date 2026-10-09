<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\CodingStyle;

use PedroTroller\CS\Fixer\AbstractFixer;
use PedroTroller\CS\Fixer\Priority;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\ConfigurableFixerTrait;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * @phpstan-type _InputConfiguration array{'max-args'?: false|int, 'max-length'?: int, 'automatic-argument-merge'?: bool, 'inline-attributes'?: bool}
 * @phpstan-type _ComputedConfiguration array{'max-args': false|int, 'max-length': int, 'automatic-argument-merge': bool, 'inline-attributes': bool}
 *
 * @implements ConfigurableFixerInterface<_InputConfiguration, _ComputedConfiguration>
 */
final class LineBreakBetweenMethodArgumentsFixer extends AbstractFixer implements ConfigurableFixerInterface, WhitespacesAwareFixerInterface
{
    /** @use ConfigurableFixerTrait<_InputConfiguration, _ComputedConfiguration> */
    use ConfigurableFixerTrait;

    public const T_TYPEHINT_SEMI_COLON = 10025;

    public function getPriority(): int
    {
        return Priority::after(MethodArgumentSpaceFixer::class);
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

    public function createConfigurationDefinition(): FixerConfigurationResolverInterface
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

            if (null === $nextIndex) {
                continue;
            }

            if (T_STRING !== $tokens[$nextIndex]->getId()) {
                continue;
            }

            $openBraceIndex = $tokens->getNextMeaningfulToken($nextIndex);

            if (null === $openBraceIndex || '(' !== $tokens[$openBraceIndex]->getContent()) {
                continue;
            }

            if (0 === $this->analyze($tokens)->getNumberOfArguments($index)) {
                $this->mergeArgs($tokens, $index);

                continue;
            }

            if ($this->analyze($tokens)->getSizeOfTheLine($index) > self::configured($this->configuration)['max-length']) {
                $this->splitArgs($tokens, $index);

                continue;
            }

            if (false !== self::configured($this->configuration)['max-args'] && $this->analyze($tokens)->getNumberOfArguments($index) > self::configured($this->configuration)['max-args']) {
                $this->splitArgs($tokens, $index);

                continue;
            }

            $clonedTokens = clone $tokens;
            $this->mergeArgs($clonedTokens, $index);

            if ($this->analyze($clonedTokens)->getSizeOfTheLine($index) > self::configured($this->configuration)['max-length']) {
                $this->splitArgs($tokens, $index);
            } elseif (self::configured($this->configuration)['automatic-argument-merge']) {
                $this->mergeArgs($tokens, $index);
            }
        }
    }

    private function splitArgs(Tokens $tokens, int $index): void
    {
        $this->mergeArgs($tokens, $index);

        $openBraceIndex = $tokens->getNextTokenOfKind($index, ['(']);

        if (null === $openBraceIndex) {
            return;
        }

        $closeBraceIndex = $this->analyze($tokens)->getClosingParenthesis($openBraceIndex);

        if (null === $closeBraceIndex) {
            return;
        }

        $afterCloseBraceIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);

        if (null !== $afterCloseBraceIndex && '{' === $tokens[$afterCloseBraceIndex]->getContent()) {
            $tokens->removeTrailingWhitespace($closeBraceIndex);
            $tokens->ensureWhitespaceAtIndex($closeBraceIndex, 1, ' ');
        }

        $afterCloseBraceIndex = $tokens->getNextMeaningfulToken($closeBraceIndex);

        if (null !== $afterCloseBraceIndex && $tokens[$afterCloseBraceIndex]->isGivenKind(self::T_TYPEHINT_SEMI_COLON)) {
            $end = $tokens->getNextTokenOfKind($closeBraceIndex, [';', '{']);

            if (null !== $end) {
                $tokens->removeLeadingWhitespace($end);

                if (';' !== $tokens[$end]->getContent()) {
                    $tokens->ensureWhitespaceAtIndex($end, 0, ' ');
                }
            }
        }

        $linebreaks = [$openBraceIndex, $closeBraceIndex - 1];

        for ($i = $openBraceIndex + 1; $i < $closeBraceIndex; ++$i) {
            if ('(' === $tokens[$i]->getContent()) {
                $i = $this->analyze($tokens)->getClosingParenthesis($i);

                if (null === $i) {
                    return;
                }
            }

            if ('[' === $tokens[$i]->getContent()) {
                $i = $this->analyze($tokens)->getClosingBracket($i);

                if (null === $i) {
                    return;
                }
            }

            if (',' === $tokens[$i]->getContent()) {
                $linebreaks[] = $i;
            }

            if (false === self::configured($this->configuration)['inline-attributes'] && $tokens[$i]->isGivenKind(T_ATTRIBUTE)) {
                $i = $this->analyze($tokens)->getClosingAttribute($i);

                if (null === $i) {
                    return;
                }

                $linebreaks[] = $i;
            }
        }

        sort($linebreaks);

        foreach (array_reverse($linebreaks, false) as $iteration => $linebreak) {
            $tokens->removeTrailingWhitespace($linebreak);

            $whitespace = match ($iteration) {
                0       => "\n".$this->analyze($tokens)->getLineIndentation($index),
                default => "\n".$this->analyze($tokens)->getLineIndentation($index).'    ',
            };

            $tokens->ensureWhitespaceAtIndex($linebreak, 1, $whitespace);
        }
    }

    private function mergeArgs(Tokens $tokens, int $index): void
    {
        $openBraceIndex = $tokens->getNextTokenOfKind($index, ['(']);

        if (null === $openBraceIndex) {
            return;
        }

        $closeBraceIndex = $this->analyze($tokens)->getClosingParenthesis($openBraceIndex);

        if (null === $closeBraceIndex) {
            return;
        }

        foreach ($tokens->findGivenKind(T_WHITESPACE, $openBraceIndex, $closeBraceIndex) as $spaceIndex => $spaceToken) {
            if ($this->isComment($tokens, $tokens->getPrevNonWhitespace($spaceIndex))) {
                continue;
            }

            if ($this->isComment($tokens, $tokens->getNextNonWhitespace($spaceIndex))) {
                continue;
            }

            $tokens[$spaceIndex] = new Token([T_WHITESPACE, ' ']);
        }

        if (!$this->isComment($tokens, $tokens->getNextNonWhitespace($openBraceIndex))) {
            $tokens->removeTrailingWhitespace($openBraceIndex);
        }

        $tokens->removeLeadingWhitespace($closeBraceIndex);

        $end = $tokens->getNextTokenOfKind($closeBraceIndex, [';', '{']);

        if (null !== $end && '{' === $tokens[$end]->getContent()) {
            $tokens->removeLeadingWhitespace($end);
            $tokens->ensureWhitespaceAtIndex($end, -1, "\n".$this->analyze($tokens)->getLineIndentation($index));
        }
    }

    private function isComment(Tokens $tokens, ?int $index): bool
    {
        return null !== $index && $tokens[$index]->isGivenKind([T_COMMENT, T_DOC_COMMENT]);
    }
}
