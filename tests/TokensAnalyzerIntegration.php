<?php

declare(strict_types=1);

namespace tests;

use Exception;
use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;

abstract class TokensAnalyzerIntegration
{
    abstract public function getCode(): string;

    abstract public function assertions(TokensAnalyzer $analyzer, Tokens $tokens): void;

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }

    protected function tokenContaining(Tokens $tokens, string $content): int
    {
        $indexes = $this->tokensContaining($tokens, $content);

        return (int) current($indexes);
    }

    /**
     * @return int[]
     */
    protected function tokensContaining(Tokens $tokens, string $content): array
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
