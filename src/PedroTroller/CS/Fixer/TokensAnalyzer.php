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

        $close     = $this->getClosingParenthesis($this->tokens, $open);
        $arguments = 1;

        for ($i = $open + 1; $i < $close; ++$i) {
            if ($this->tokens[$i]->equals('(')) {
                $i = $this->getClosingParenthesis($this->tokens, $i);
            }

            if ($this->tokens[$i]->equals('[')) {
                $i = $this->getClosingBracket($this->tokens, $i);
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

        if (false === $this->tokens[$next]->isGivenKind(TokenSignatures::TYPINT_DOUCLE_DOTS)) {
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
    private function getClosingParenthesis($index)
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
     * @return int | null
     */
    private function getClosingBracket($index)
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
    private function getClosingCurlyBracket($index)
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
}
