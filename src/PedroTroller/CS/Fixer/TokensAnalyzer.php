<?php

namespace PedroTroller\CS\Fixer;

use Exception;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer as PhpCsFixerTokensAnalyzer;

// @method getClassyElements()
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
        return \call_user_func_array([$this->analyzer, $name], $arguments);
    }

    /*
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

    /*
     * @param int $index
     *
     * @return int
     */
    public function getNumberOfArguments($index)
    {
        return \count($this->getMethodArguments($index));
    }

    /*
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

    /*
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
     *
     * @return null|string|array
     */
    public function getReturnedType($index)
    {
        if (false === $this->tokens[$index]->isGivenKind(T_FUNCTION)) {
            throw new Exception(sprintf('Expected token: T_FUNCTION Token %d id contains %s.', $index, $this->tokens[$index]->getContent()));
        }

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

        $next = $optionnal
            ? $this->tokens->getNextMeaningfulToken($next)
            : $next
        ;

        do {
            $return = $this->tokens[$next]->getContent();
            ++$next;

            if ($this->tokens[$next]->isWhitespace() || $this->tokens[$next]->equals(';')) {
                return $optionnal
                    ? [$return, null]
                    : $return;
            }
        } while (false === $this->tokens[$index]->equals(['{', ';']));
    }

    /*
     * @param int $index
     *
     * @return int|null
     */
    public function getBeginningOfTheLine($index)
    {
        for ($i = $index; $i >= 0; --$i) {
            if (false !== mb_strpos($this->tokens[$i]->getContent(), "\n")) {
                return $i;
            }
        }
    }

    /*
     * @param int $index
     *
     * @return int|null
     */
    public function getEndOfTheLine($index)
    {
        for ($i = $index; $i < $this->tokens->count(); ++$i) {
            if (false !== mb_strpos($this->tokens[$i]->getContent(), "\n")) {
                return $i;
            }
        }
    }

    /*
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
     * @return int|null
     */
    public function endOfTheStatement($index)
    {
        do {
            $index = $this->tokens->getNextMeaningfulToken($index);

            if (null === $index) {
                return null;
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
        } while (false === $this->tokens[$index]->equals('}'));

        return $index;
    }

    /**
     * @param int $index
     *
     * @return int | null
     */
    public function getClosingParenthesis($index)
    {
        if (false === $this->tokens[$index]->equals('(')) {
            throw new Exception(sprintf('Expected token: (. Token %d id contains %s.', $index, $this->tokens[$index]->getContent()));
        }

        for ($i = $index + 1; $i < $this->tokens->count(); ++$i) {
            if ($this->tokens[$i]->equals('(')) {
                $i = $this->getClosingParenthesis($i);

                if (null === $i) {
                    return null;
                }

                continue;
            }

            if ($this->tokens[$i]->equals(')')) {
                return $i;
            }
        }
    }

    /*
     * @param int $index
     *
     * @return int | null
     */
    public function getClosingBracket($index)
    {
        if (false === $this->tokens[$index]->equals('[')) {
            throw new Exception(sprintf('Expected token: [. Token %d id contains %s.', $index, $this->tokens[$index]->getContent()));
        }

        for ($i = $index + 1; $i < $this->tokens->count(); ++$i) {
            if ($this->tokens[$i]->equals('[')) {
                $i = $this->getClosingBracket($i);

                if (null === $i) {
                    return null;
                }

                continue;
            }

            if ($this->tokens[$i]->equals(']')) {
                return $i;
            }
        }
    }

    /*
     * @param int $index
     *
     * @return int | null
     */
    public function getClosingCurlyBracket($index)
    {
        if (false === $this->tokens[$index]->equals('{')) {
            throw new Exception(sprintf('Expected token: {. Token %d id contains %s.', $index, $this->tokens[$index]->getContent()));
        }

        for ($i = $index + 1; $i < $this->tokens->count(); ++$i) {
            if ($this->tokens[$i]->equals('{')) {
                $i = $this->getClosingCurlyBracket($i);

                if (null === $i) {
                    return null;
                }

                continue;
            }

            if ($this->tokens[$i]->equals('}')) {
                return $i;
            }
        }
    }

    /*
     * @param int $index
     *
     * @return bool
     */
    public function isInsideSwitchCase($index)
    {
        $switch = null;
        $ids    = array_keys($this->tokens->toArray());

        $switches  = $this->findAllSequences([[[T_SWITCH]]]);
        $intervals = [];

        foreach ($switches as $i => $switch) {
            $start = $this->tokens->getNextTokenOfKind($i, ['{']);
            $end   = $this->getClosingCurlyBracket($start);

            $intervals[] = [$start, $end];
        }

        foreach ($intervals as $interval) {
            list($start, $end) = $interval;

            if ($index >= $start && $index <= $end) {
                return true;
            }
        }

        return false;
    }

    /*
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

    /**
     * @return array
     */
    public function findAllSequences(array $seqs)
    {
        $sequences = [];

        foreach ($seqs as $seq) {
            $index = 0;

            do {
                $extract = $this->tokens->findSequence($seq, (int) $index);

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
}
