<?php

namespace tests;

use Exception;
use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

abstract class TokensAnalyzerIntegration
{
    // @return string
    abstract public function getCode();

    abstract public function assertions(TokensAnalyzer $analyzer, Tokens $tokens);

    // @return int
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }

    /*
     * @param string $content
     *
     * @return int
     */
    protected function tokenContaining(Tokens $tokens, $content)
    {
        foreach ($tokens as $index => $token) {
            if ($content === $token->getContent()) {
                return $index;
            }
        }

        throw new Exception(sprintf('There is no token containing %s.', $content));
    }
}
