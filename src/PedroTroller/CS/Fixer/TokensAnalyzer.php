<?php

namespace PedroTroller\CS\Fixer;

use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer as PhpCsFixerTokensAnalyzer;

/**
 * @method getClassyElements()
 */
final class TokensAnalyzer
{
    /**
     * @var Tokens
     */
    private $tokens;

    /**
     * @var PhpCsFixerTokensAnalyzer
     */
    private $analyzer;

    public function __construct(Tokens $tokens)
    {
        $this->tokens   = $tokens;
        $this->analyzer = new PhpCsFixerTokensAnalyzer($tokens);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->analyzer, $name], $arguments);
    }

    /**
     * @param int $index
     *
     * @return array
     */
    public function getMethodArguments($index)
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

            if ($this->tokens[$next]->equals('=')) {
                $argumentAsDefault = true;
                $value             = $this->tokens->getNextMeaningfulToken($next);
                $argumentNullable  = 'null' === $this->tokens[$value]->getContent();
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

    /**
     * @param int $index
     *
     * @return int
     */
    public function getNumberOfArguments($index)
    {
        if (T_FUNCTION !== $this->tokens[$index]->getId()) {
            return 0;
        }

        $open = $this->tokens->getNextTokenOfKind($index, ['(']);

        if ($this->tokens[$this->tokens->getNextMeaningfulToken($open)]->equals(')')) {
            return 0;
        }

        $close     = $this->getClosingParenthesis($open);
        $arguments = 1;

        for ($i = $open + 1; $i < $close; ++$i) {
            if ($this->tokens[$i]->equals('(')) {
                $i = $this->getClosingParenthesis($i);
            }

            if ($this->tokens[$i]->equals('[')) {
                $i = $this->getClosingBracket($i);
            }

            if ($this->tokens[$i]->equals(',')) {
                ++$arguments;
            }
        }

        return $arguments;
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
                return;
            }

            switch (true) {
                case $this->tokens[$index]->equals('('):
                    $index = $this->getClosingParenthesis($index);

                    break;
                case $this->tokens[$index]->equals('['):
                    $index = $this->getClosingBracket($index);

                    break;
                case $this->tokens[$index]->equals('{'):
                    $index = $this->getClosingCurlyBracket($index);

                    break;
                case $this->tokens[$index]->equals(';'):
                    return;
            }
        } while (false === $this->tokens[$index]->equals(','));

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
                return;
            }

            switch (true) {
                case $this->tokens[$index]->equals('('):
                    $index = $this->getClosingParenthesis($index);

                    break;
                case $this->tokens[$index]->equals('['):
                    $index = $this->getClosingBracket($index);

                    break;
                case $this->tokens[$index]->equals('{'):
                    $index = $this->getClosingCurlyBracket($index);

                    break;
            }
        } while (false === $this->tokens[$index]->equals(';'));

        return $index;
    }

    /**
     * @param int $index
     */
    public function getReturnedType($index)
    {
        $methodName       = $this->tokens->getNextMeaningfulToken($index);
        $openParenthesis  = $this->tokens->getNextMeaningfulToken($methodName);
        $closeParenthesis = $this->getClosingParenthesis($openParenthesis);

        $next = $this->tokens->getNextMeaningfulToken($closeParenthesis);

        if (null === $next) {
            return;
        }

        if (false === $this->tokens[$next]->isGivenKind(TokenSignatures::TYPINT_DOUBLE_DOTS)) {
            return;
        }

        $next = $this->tokens->getNextMeaningfulToken($next);

        if (null === $next) {
            return;
        }

        $optionnal = $this->tokens[$next]->isGivenKind(TokenSignatures::TYPINT_OPTIONAL);

        if ($optionnal) {
            $next = $this->tokens->getNextMeaningfulToken($next);
        }

        $return = null;

        do {
            $return = $this->tokens[$next]->getContent();
            ++$next;

            if ($this->tokens[$next]->isWhitespace()) {
                return $optionnal
                    ? [$return, null]
                    : $return;
            }
        } while (false === $this->tokens[$index]->equals(['{', ';']));
    }

    /**
     * @param int $index
     *
     * @return int | null
     */
    public function getClosingParenthesis($index)
    {
        $opening = 0;

        for ($i = $index + 1; $i < $this->tokens->count(); ++$i) {
            if ($this->tokens[$i]->equals('(')) {
                ++$opening;
            }

            if ($this->tokens[$i]->equals(')')) {
                if ($opening > 0) {
                    --$opening;
                }

                return $i;
            }
        }
    }

    /**
     * @param int $index
     *
     * @return int
     */
    public function getBeginningOfTheLine($index)
    {
        for ($i = $index; $i >= 0; --$i) {
            if (false !== mb_strpos($this->tokens[$i]->getContent(), "\n")) {
                return $i;
            }
        }
    }

    /**
     * @param int $index
     *
     * @return int
     */
    public function getEndOfTheLine($index)
    {
        for ($i = $index; $i < $this->tokens->count(); ++$i) {
            if (false !== mb_strpos($this->tokens[$i]->getContent(), "\n")) {
                return $i;
            }
        }
    }

    /**
     * @param int $index
     *
     * @return int
     */
    public function getSizeOfTheLine($index)
    {
        $start = $this->getBeginningOfTheLine($index);
        $end   = $this->getEndOfTheLine($index);
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

    /**
     * @param int $index
     *
     * @return int | null
     */
    public function getClosingBracket($index)
    {
        $opening = 0;

        for ($i = $index + 1; $i < $this->tokens->count(); ++$i) {
            if ($this->tokens[$i]->equals('[')) {
                ++$opening;
            }

            if ($this->tokens[$i]->equals(']')) {
                if ($opening > 0) {
                    --$opening;
                }

                return $i;
            }
        }
    }

    /**
     * @param int $index
     *
     * @return int | null
     */
    public function getClosingCurlyBracket($index)
    {
        $opening = 0;

        for ($i = $index + 1; $i < $this->tokens->count(); ++$i) {
            if ($this->tokens[$i]->equals('{')) {
                ++$opening;
            }

            if ($this->tokens[$i]->equals('}')) {
                if ($opening > 0) {
                    --$opening;
                }

                return $i;
            }
        }
    }

    /**
     * @param int $index
     *
     * @return bool
     */
    public function isInsideSwitchCase($index)
    {
        $switch = null;
        $ids    = array_keys($this->tokens->toArray());

        for ($i = $index; $i >= current($ids); --$i) {
            if (null !== $switch) {
                continue;
            }

            if (T_SWITCH === $this->tokens[$i]->getId()) {
                $switch = $i;
            }
        }

        if (null === $switch) {
            return false;
        }

        $open  = $this->tokens->getNextTokenOfKind($index, ['{']);
        $close = $this->getClosingCurlyBracket($open + 1);

        return $open < $index && $close > $index;
    }

    /**
     * @param int $index
     *
     * @return string
     */
    public function getLineIndentation($index)
    {
        $start = $this->getBeginningOfTheLine($index);
        $token = $this->tokens[$start];
        $parts = explode("\n", $token->getContent());

        return end($parts);
    }
}
