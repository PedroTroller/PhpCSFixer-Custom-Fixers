<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use LogicException;
use PhpCsFixer\AbstractFixer as PhpCsFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

abstract class AbstractFixer extends PhpCsFixer
{
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return true;
    }

    public function getName(): string
    {
        return \sprintf('PedroTroller/%s', parent::getName());
    }

    /**
     * @return list<null|array<string, mixed>>
     */
    public function getSampleConfigurations(): array
    {
        return [
            [],
        ];
    }

    abstract public function getSampleCode(): string;

    abstract public function getDocumentation(): string;

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            $this->getDocumentation(),
            array_map(
                fn (?array $configutation = null): CodeSample => new CodeSample($this->getSampleCode(), $configutation),
                $this->getSampleConfigurations()
            )
        );
    }

    public function isDeprecated(): bool
    {
        return false;
    }

    public function getDeprecationReplacement(): ?string
    {
        return null;
    }

    /**
     * php-cs-fixer configures every configurable fixer with its defaults in its constructor,
     * so the configuration is only null if that contract is broken.
     *
     * @template TConfiguration of array<string, mixed>
     *
     * @param null|TConfiguration $configuration
     *
     * @return TConfiguration
     */
    protected static function configured(?array $configuration): array
    {
        if (null === $configuration) {
            throw new LogicException(\sprintf('%s is used before being configured.', static::class));
        }

        return $configuration;
    }

    protected function analyze(Tokens $tokens): TokensAnalyzer
    {
        return new TokensAnalyzer($tokens);
    }

    /**
     * @param string|string[] $fqcn
     */
    protected function hasUseStatements(Tokens $tokens, $fqcn): bool
    {
        return null !== $this->getUseStatements($tokens, $fqcn);
    }

    /**
     * @param string|string[] $fqcn
     *
     * @return null|array<int, Token>
     */
    protected function getUseStatements(Tokens $tokens, $fqcn): ?array
    {
        if (false === \is_array($fqcn)) {
            $fqcn = explode('\\', $fqcn);
        }

        $sequence = [[T_USE]];
        foreach ($fqcn as $component) {
            $sequence = array_merge(
                $sequence,
                [[T_STRING, $component], [T_NS_SEPARATOR]]
            );
        }

        $sequence[\count($sequence) - 1] = ';';

        return $tokens->findSequence($sequence);
    }

    /**
     * @param string|string[] $fqcn
     */
    protected function extendsClass(Tokens $tokens, $fqcn): bool
    {
        if (false === \is_array($fqcn)) {
            $fqcn = explode('\\', $fqcn);
        }

        if (false === $this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }

        $class = array_pop($fqcn);

        if (null === $class) {
            return false;
        }

        return null !== $tokens->findSequence([
            [T_CLASS],
            [T_STRING],
            [T_EXTENDS],
            [T_STRING, $class],
        ]);
    }

    /**
     * @param string|string[] $fqcn
     */
    protected function implementsInterface(Tokens $tokens, $fqcn): bool
    {
        if (false === \is_array($fqcn)) {
            $fqcn = explode('\\', $fqcn);
        }

        if (false === $this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }

        $interface = array_pop($fqcn);

        if (null === $interface) {
            return false;
        }

        return null !== $tokens->findSequence([
            [T_CLASS],
            [T_STRING],
            [T_IMPLEMENTS],
            [T_STRING, $interface],
        ]);
    }

    /**
     * @return array<Token>
     */
    protected function getComments(Tokens $tokens): array
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
