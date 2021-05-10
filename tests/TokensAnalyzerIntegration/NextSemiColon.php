<?php

declare(strict_types=1);

namespace tests\TokensAnalyzerIntegration;

use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use tests\TokensAnalyzerIntegration;
use Webmozart\Assert\Assert;

final class NextSemiColon extends TokensAnalyzerIntegration
{
    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return <<<'PHP'
            <?php

            namespace Project\TheNamespace;

            class TheClass
            {
                public function theFunction()
                {
                    $requests = Promise\settle($requests)
                        ->then(function (array $states) {
                            return array_map(function (array $state) {
                                if ($state['state'] === PromiseInterface::FULFILLED) {
                                    return preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $state['value']->getBody()->getContents());
                                }

                                $uri = (string) $state['reason']->getRequest()->getUri();

                                $this->logger->error("Error while trying to fetch article whitelist from `{$uri}`", [
                                    'error' => $state['reason'],
                                ]);
                            }, $states);
                        })
                    ;
                }
            }
            PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function assertions(TokensAnalyzer $analyzer, Tokens $tokens): void
    {
        Assert::eq(
            $analyzer->getNextSemiColon(
                $this->tokenContaining($tokens, 'array_map')
            ),
            max($this->tokensContaining($tokens, '$states')) + 2
        );
    }
}
