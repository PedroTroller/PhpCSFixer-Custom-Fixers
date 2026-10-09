<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use Exception;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer as PhpCsFixerTokensAnalyzer;

/**
 * @method array<int, array{classIndex: int, token: Token, type: string}> getClassyElements()
 *
 * @phpstan-type MethodArgument array{type: null|string, name: string, nullable: bool, asDefault: bool}
 * @phpstan-type ClassElementType 'use_trait'|'constant'|'property'|'construct'|'destruct'|'magic'|'method'|array{'phpunit', string}
 * @phpstan-type ClassElement (
 *     array{start: int, visibility: string, static: bool, type: 'method', methodName: string, end: int, comment: null|string}
 *     | array{start: int, visibility: string, static: bool, type: 'property', propertyName: string, end: int, comment: null|string}
 *     | array{start: int, visibility: string, static: bool, type: 'use_trait'|'constant'|'construct'|'destruct'|'magic'|array{'phpunit', string}, end: int, comment: null|string}
 * )
 */
final class TokensAnalyzer
{
    private readonly PhpCsFixerTokensAnalyzer $analyzer;

    public function __construct(private Tokens $tokens)
    {
        $this->analyzer = new PhpCsFixerTokensAnalyzer($this->tokens);
    }

    /**
     * @param list<mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->analyzer->{$name}(...$arguments);
    }

    /**
     * @return array<int, MethodArgument>
     */
    public function getMethodArguments(int $index): array
    {
        $methodName       = $this->tokens->getNextMeaningfulToken($index);
        $openParenthesis  = $this->tokens->getNextMeaningfulToken($methodName);
        $closeParenthesis = $this->getClosingParenthesis($openParenthesis);

        $arguments = [];

        for ($position = $openParenthesis + 1; $position < $closeParenthesis; ++$position) {
            $token = $this->tokens[$position];

            if ($token->isWhitespace()) {
                continue;
            }

            $argumentType      = null;
            $argumentName      = $position;
            $argumentAsDefault = false;
            $argumentNullable  = false;

            if (!preg_match('/^\$.+/', $this->tokens[$argumentName]->getContent())) {
                do {
                    if (false === $this->tokens[$argumentName]->isWhitespace()) {
                        $argumentType .= $this->tokens[$argumentName]->getContent();
                    }

                    ++$argumentName;
                } while (!preg_match('/^\$.+/', $this->tokens[$argumentName]->getContent()));
            }

            $next = $this->tokens->getNextMeaningfulToken($argumentName);

            if (null !== $next && '=' === $this->tokens[$next]->getContent()) {
                $argumentAsDefault = true;
                $value             = $this->tokens->getNextMeaningfulToken($next);
                $argumentNullable  = null !== $value && 'null' === $this->tokens[$value]->getContent();
            }

            $arguments[$position] = [
                'type'      => $argumentType,
                'name'      => $this->tokens[$argumentName]->getContent(),
                'nullable'  => $argumentNullable,
                'asDefault' => $argumentAsDefault,
            ];

            $nextComma = $this->getNextComma($position);

            if (null === $nextComma) {
                return $arguments;
            }

            $position = $nextComma;
        }

        return $arguments;
    }

    public function getNumberOfArguments(int $index): int
    {
        return \count($this->getMethodArguments($index));
    }

    /**
     * @param int $index
     *
     * @return null|int
     */
    public function getNextComma($index)
    {
        do {
            $index = $this->tokens->getNextMeaningfulToken($index);

            if (null === $index) {
                return null;
            }

            switch (true) {
                case '(' === $this->tokens[$index]->getContent():
                    $index = $this->getClosingParenthesis($index);

                    break;

                case '[' === $this->tokens[$index]->getContent():
                    $index = $this->getClosingBracket($index);

                    break;

                case '{' === $this->tokens[$index]->getContent():
                    $index = $this->getClosingCurlyBracket($index);

                    break;

                case ';' === $this->tokens[$index]->getContent():
                    return null;
            }

            if (null === $index) {
                return null;
            }
        } while (',' !== $this->tokens[$index]->getContent());

        return $index;
    }

    /**
     * @param int $index
     *
     * @return null|int
     */
    public function getNextSemiColon($index)
    {
        do {
            $index = $this->tokens->getNextMeaningfulToken($index);

            if (null === $index) {
                return null;
            }

            switch (true) {
                case '(' === $this->tokens[$index]->getContent():
                    $index = $this->getClosingParenthesis($index);

                    break;

                case '[' === $this->tokens[$index]->getContent():
                    $index = $this->getClosingBracket($index);

                    break;

                case '{' === $this->tokens[$index]->getContent():
                    $index = $this->getClosingCurlyBracket($index);

                    break;
            }

            if (null === $index) {
                return null;
            }
        } while (';' !== $this->tokens[$index]->getContent());

        return $index;
    }

    /**
     * @param int $index
     *
     * @return null|array{string, null}|string
     */
    public function getReturnedType($index)
    {
        if (false === $this->tokens[$index]->isGivenKind(T_FUNCTION)) {
            throw new Exception(\sprintf('Expected token: T_FUNCTION Token %d id contains %s.', $index, $this->tokens[$index]->getContent()));
        }

        $methodName       = $this->tokens->getNextMeaningfulToken($index);
        $openParenthesis  = $this->tokens->getNextMeaningfulToken($methodName);
        $closeParenthesis = $this->getClosingParenthesis($openParenthesis);

        $next = $this->tokens->getNextMeaningfulToken($closeParenthesis);

        if (null === $next) {
            return null;
        }

        if (false === $this->tokens[$next]->isGivenKind(TokenSignatures::TYPINT_DOUBLE_DOTS)) {
            return null;
        }

        $next = $this->tokens->getNextMeaningfulToken($next);

        if (null === $next) {
            return null;
        }

        $optionnal = $this->tokens[$next]->isGivenKind(TokenSignatures::TYPINT_OPTIONAL);

        $next = $optionnal
            ? $this->tokens->getNextMeaningfulToken($next)
            : $next;

        if (null === $next) {
            return null;
        }

        do {
            $return = $this->tokens[$next]->getContent();
            ++$next;

            if ($this->tokens[$next]->isWhitespace() || ';' === $this->tokens[$next]->getContent()) {
                return $optionnal
                    ? [$return, null]
                    : $return;
            }
        } while (false === \in_array($this->tokens[$index]->getContent(), ['{', ';'], true));

        return null;
    }

    /**
     * @param int $index
     *
     * @return null|int
     */
    public function getBeginningOfTheLine($index)
    {
        for ($i = $index; $i >= 0; --$i) {
            if (str_contains($this->tokens[$i]->getContent(), "\n")) {
                return $i;
            }
        }

        return null;
    }

    /**
     * @param int $index
     *
     * @return null|int
     */
    public function getEndOfTheLine($index)
    {
        for ($i = $index; $i < $this->tokens->count(); ++$i) {
            if (str_contains($this->tokens[$i]->getContent(), "\n")) {
                return $i;
            }
        }

        return null;
    }

    /**
     * @param int $index
     */
    public function getSizeOfTheLine($index): int
    {
        $start = $this->getBeginningOfTheLine($index) ?? 0;
        $end   = $this->getEndOfTheLine($index) ?? $this->tokens->count() - 1;
        $size  = 0;

        $parts = explode("\n", $this->tokens[$start]->getContent());
        $size += mb_strlen(end($parts));

        $parts = explode("\n", $this->tokens[$end]->getContent());
        $size += mb_strlen(current($parts));

        for ($i = $start + 1; $i < $end; ++$i) {
            $size += mb_strlen($this->tokens[$i]->getContent());
        }

        return $size;
    }

    public function endOfTheStatement(int $index): ?int
    {
        do {
            $index = $this->tokens->getNextMeaningfulToken($index);

            if (null === $index) {
                return null;
            }

            switch (true) {
                case '(' === $this->tokens[$index]->getContent():
                    $index = $this->getClosingParenthesis($index);

                    break;

                case '[' === $this->tokens[$index]->getContent():
                    $index = $this->getClosingBracket($index);

                    break;

                case '{' === $this->tokens[$index]->getContent():
                    $index = $this->getClosingCurlyBracket($index);

                    break;
            }

            if (null === $index) {
                return null;
            }
        } while ('}' !== $this->tokens[$index]->getContent());

        return $index;
    }

    /**
     * @param int $index
     *
     * @return null|int
     */
    public function getClosingParenthesis($index)
    {
        if ('(' !== $this->tokens[$index]->getContent()) {
            throw new Exception(\sprintf('Expected token: (. Token %d id contains %s.', $index, $this->tokens[$index]->getContent()));
        }

        for ($i = $index + 1; $i < $this->tokens->count(); ++$i) {
            if ('(' === $this->tokens[$i]->getContent()) {
                $i = $this->getClosingParenthesis($i);

                if (null === $i) {
                    return null;
                }

                continue;
            }

            if (')' === $this->tokens[$i]->getContent()) {
                return $i;
            }
        }

        return null;
    }

    /**
     * @param int $index
     *
     * @return null|int
     */
    public function getClosingBracket($index)
    {
        if ('[' !== $this->tokens[$index]->getContent()) {
            throw new Exception(\sprintf('Expected token: [. Token %d id contains %s.', $index, $this->tokens[$index]->getContent()));
        }

        for ($i = $index + 1; $i < $this->tokens->count(); ++$i) {
            if ('[' === $this->tokens[$i]->getContent()) {
                $i = $this->getClosingBracket($i);

                if (null === $i) {
                    return null;
                }

                continue;
            }

            if (']' === $this->tokens[$i]->getContent()) {
                return $i;
            }
        }

        return null;
    }

    /**
     * @param int $index
     *
     * @return null|int
     */
    public function getClosingCurlyBracket($index)
    {
        if ('{' !== $this->tokens[$index]->getContent()) {
            throw new Exception(\sprintf('Expected token: {. Token %d id contains %s.', $index, $this->tokens[$index]->getContent()));
        }

        for ($i = $index + 1; $i < $this->tokens->count(); ++$i) {
            if ('{' === $this->tokens[$i]->getContent()) {
                $i = $this->getClosingCurlyBracket($i);

                if (null === $i) {
                    return null;
                }

                continue;
            }

            if ('}' === $this->tokens[$i]->getContent()) {
                return $i;
            }
        }

        return null;
    }

    /**
     * @param int $index
     *
     * @return null|int
     */
    public function getClosingAttribute($index)
    {
        if (false === $this->tokens[$index]->isGivenKind(T_ATTRIBUTE)) {
            throw new Exception(\sprintf('Expected token: T_ATTRIBUTE Token %d id contains %s.', $index, $this->tokens[$index]->getContent()));
        }

        for ($i = $index + 1; $i < $this->tokens->count(); ++$i) {
            if ($this->tokens[$i]->isGivenKind(T_ATTRIBUTE)) {
                $i = $this->getClosingAttribute($i);

                if (null === $i) {
                    return null;
                }

                continue;
            }

            if ($this->tokens[$i]->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                return $i;
            }
        }

        return null;
    }

    /**
     * @param int $index
     */
    public function isInsideSwitchCase($index): bool
    {
        $switches  = $this->findAllSequences([[[T_SWITCH]]]);
        $intervals = [];

        foreach ($switches as $i => $switch) {
            $start = $this->tokens->getNextTokenOfKind($i, ['{']);
            $end   = $this->getClosingCurlyBracket($start);

            $intervals[] = [$start, $end];
        }

        foreach ($intervals as $interval) {
            [$start, $end] = $interval;

            if ($index >= $start && $index <= $end) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int $index
     */
    public function getLineIndentation($index): string
    {
        $start = $this->getBeginningOfTheLine($index);

        if (null === $start) {
            return '';
        }

        $token = $this->tokens[$start];
        $parts = explode("\n", $token->getContent());

        return end($parts);
    }

    /**
     * @param list<non-empty-list<array{0: int, 1?: string}|string|Token>> $seqs
     * @param null|int                                                     $start
     *
     * @return array<int, array<int, Token>>
     */
    public function findAllSequences(array $seqs, $start = null, ?int $end = null): array
    {
        $sequences = [];

        foreach ($seqs as $seq) {
            $index = $start ?: 0;

            do {
                $extract = $this->tokens->findSequence($seq, (int) $index, $end);

                if (null !== $extract) {
                    $keys                    = array_keys($extract);
                    $index                   = end($keys) + 1;
                    $sequences[reset($keys)] = $extract;
                }
            } while (null !== $extract);
        }

        ksort($sequences);

        return $sequences;
    }

    /**
     * @param null|int $startIndex index of the opening curly brace of the class body, or null for the first class of the file
     *
     * @return list<ClassElement>
     */
    public function getElements($startIndex = null)
    {
        if (null === $startIndex) {
            foreach ($this->tokens as $startIndex => $token) {
                if (!$token->isClassy()) {
                    continue;
                }

                $startIndex = $this->tokens->getNextTokenOfKind($startIndex, ['{']);

                break;
            }
        }

        ++$startIndex;
        $elements = [];

        while (true) {
            $visibility = 'public';
            $static     = false;

            for ($i = $startIndex;; ++$i) {
                $token = $this->tokens[$i];

                if ('}' === $token->getContent()) {
                    return $elements;
                }

                if ($token->isGivenKind(T_STATIC)) {
                    $static = true;

                    continue;
                }

                if ($token->isGivenKind([T_PROTECTED, T_PRIVATE])) {
                    $visibility = mb_strtolower($token->getContent());

                    continue;
                }

                if ($token->isGivenKind([CT::T_USE_TRAIT, T_CONST, T_VARIABLE, T_FUNCTION])) {
                    break;
                }
            }

            $type    = $this->detectElementType($i);
            $end     = $this->findElementEnd($i);
            $comment = isset($this->tokens[$startIndex + 1]) && $this->tokens[$startIndex + 1]->isComment()
                ? $this->tokens[$startIndex + 1]->getContent()
                : null;

            $elements[] = match ($type) {
                'method' => [
                    'start'      => $startIndex,
                    'visibility' => $visibility,
                    'static'     => $static,
                    'type'       => $type,
                    'methodName' => $this->tokens[$this->getNextMeaningfulTokenOrFail($i)]->getContent(),
                    'end'        => $end,
                    'comment'    => $comment,
                ],
                'property' => [
                    'start'        => $startIndex,
                    'visibility'   => $visibility,
                    'static'       => $static,
                    'type'         => $type,
                    'propertyName' => $token->getContent(),
                    'end'          => $end,
                    'comment'      => $comment,
                ],
                default => [
                    'start'      => $startIndex,
                    'visibility' => $visibility,
                    'static'     => $static,
                    'type'       => $type,
                    'end'        => $end,
                    'comment'    => $comment,
                ],
            };

            $startIndex = $end + 1;
        }
    }

    /**
     * @return ClassElementType
     */
    private function detectElementType(int $index): array|string
    {
        $token = $this->tokens[$index];

        if ($token->isGivenKind(CT::T_USE_TRAIT)) {
            return 'use_trait';
        }

        if ($token->isGivenKind(T_CONST)) {
            return 'constant';
        }

        if ($token->isGivenKind(T_VARIABLE)) {
            return 'property';
        }

        $nameToken = $this->tokens[$this->getNextMeaningfulTokenOrFail($index)];

        if ($nameToken->equals([T_STRING, '__construct'], false)) {
            return 'construct';
        }

        if ($nameToken->equals([T_STRING, '__destruct'], false)) {
            return 'destruct';
        }

        if (
            $nameToken->equalsAny([
                [T_STRING, 'setUpBeforeClass'],
                [T_STRING, 'tearDownAfterClass'],
                [T_STRING, 'setUp'],
                [T_STRING, 'tearDown'],
            ], false)
        ) {
            return ['phpunit', mb_strtolower($nameToken->getContent())];
        }

        if ('__' === mb_substr($nameToken->getContent(), 0, 2)) {
            return 'magic';
        }

        return 'method';
    }

    private function findElementEnd(int $index): int
    {
        $next = $this->tokens->getNextTokenOfKind($index, ['{', ';']);

        if (null === $next) {
            throw new Exception(\sprintf('Expected token: { or ; after class element at index %d.', $index));
        }

        $index = $next;

        if ('{' === $this->tokens[$index]->getContent()) {
            $index = $this->tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        }

        for (++$index; $this->tokens[$index]->isWhitespace(" \t") || $this->tokens[$index]->isComment(); ++$index);

        --$index;

        return $this->tokens[$index]->isWhitespace() ? $index - 1 : $index;
    }

    private function getNextMeaningfulTokenOrFail(int $index): int
    {
        $next = $this->tokens->getNextMeaningfulToken($index);

        if (null === $next) {
            throw new Exception(\sprintf('Expected a meaningful token after index %d.', $index));
        }

        return $next;
    }
}
