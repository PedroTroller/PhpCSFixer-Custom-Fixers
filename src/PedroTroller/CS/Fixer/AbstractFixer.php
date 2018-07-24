<?php

namespace PedroTroller\CS\Fixer;

use PhpCsFixer\AbstractFixer as PhpCsFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Tokenizer\Tokens;

abstract class AbstractFixer extends PhpCsFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('PedroTroller/%s', parent::getName());
    }

    /**
     * @return array
     */
    public function getSampleConfigurations()
    {
        return [
            null,
        ];
    }

    /**
     * @return string
     */
    abstract public function getSampleCode();

    /**
     * @return string
     */
    abstract public function getDocumentation();

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            $this->getDocumentation(),
            array_map(
                function (array $configutation = null) {
                    return new CodeSample($this->getSampleCode(), $configutation);
                },
                $this->getSampleConfigurations()
            )
        );
    }

    /**
     * @return bool
     */
    public function isDeprecated()
    {
        return false;
    }

    /**
     * @return null|string
     */
    public function getDeprecationReplacement()
    {
    }

    /**
     * @return TokensAnalyzer
     */
    protected function analyze(Tokens $tokens)
    {
        return new TokensAnalyzer($tokens);
    }

    /**
     * @param Tokens          $tokens
     * @param string|string[] $fqcn
     *
     * @return bool
     */
    protected function hasUseStatements(Tokens $tokens, $fqcn)
    {
        return null !== $this->getUseStatements($tokens, $fqcn);
    }

    /**
     * @param Tokens          $tokens
     * @param string|string[] $fqcn
     *
     * @return null|array
     */
    protected function getUseStatements(Tokens $tokens, $fqcn)
    {
        if (false === is_array($fqcn)) {
            $fqcn = explode('\\', $fqcn);
        }
        $sequence = [[T_USE]];
        foreach ($fqcn as $component) {
            $sequence = array_merge(
                $sequence,
                [[T_STRING, $component], [T_NS_SEPARATOR]]
            );
        }
        $sequence[count($sequence) - 1] = ';';

        return $tokens->findSequence($sequence);
    }

    /**
     * @param Tokens          $tokens
     * @param string|string[] $fqcn
     *
     * @return bool
     */
    protected function extendsClass(Tokens $tokens, $fqcn)
    {
        if (false === is_array($fqcn)) {
            $fqcn = explode('\\', $fqcn);
        }

        if (false === $this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }

        return null !== $tokens->findSequence([
            [T_CLASS],
            [T_STRING],
            [T_EXTENDS],
            [T_STRING, array_pop($fqcn)],
        ]);
    }

    /**
     * @return PhpCsFixer\Tokenizer\Token[]
     */
    protected function getComments(Tokens $tokens)
    {
        $comments = [];

        foreach ($tokens as $index => $token) {
            if ($token->isComment()) {
                $comments[$index] = $token;
            }
        }

        return $comments;
    }
}
