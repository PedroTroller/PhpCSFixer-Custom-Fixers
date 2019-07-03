<?php

declare(strict_types=1);

namespace tests;

use Exception;
use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

abstract class TokensAnalyzerIntegration
{
    /**
     * @return string
     */
    abstract public function getCode();

    abstract public function assertions(TokensAnalyzer $analyzer, Tokens $tokens);

    /**
     * @return int
     */
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }

    /**
     * @param string $content
     *
     * @return int
     */
    protected function tokenContaining(Tokens $tokens, $content)
    {
        $indexes = $this->tokensContaining($tokens, $content);

        return current($indexes);
    }

    /**
     * @param string $content
     *
     * @return int[]
     */
    protected function tokensContaining(Tokens $tokens, $content)
    {
        $indexes = [];

        foreach ($tokens as $index => $token) {
            if ($content === $token->getContent()) {
                $indexes[] = $index;
            }
        }

        if (empty($indexes)) {
            throw new Exception(sprintf('There is no token containing %s.', $content));
        }

        return $indexes;
    }
}
