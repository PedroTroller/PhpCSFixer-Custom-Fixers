<?php

namespace PedroTroller\CS\Fixer;

use Symfony\CS\AbstractFixer as BaseFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

abstract class AbstractFixer extends BaseFixer
{
    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return self::CONTRIB_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        $class     = get_class($this);
        $parts     = explode('\\', $class);
        $shortname = end($parts);

        if (true === $this->endsWith($shortname, 'Fixer')) {
            $shortname = substr($shortname, 0, -5);
        }

        $shortname = preg_replace('/(?!^)([A-Z])/', '_$1', $shortname);

        return strtolower($shortname);
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    protected function endsWith($haystack, $needle)
    {
        if (true === empty($needle)) {
            return true;
        }

        if (0 >= ($temp = strlen($haystack) - strlen($needle))) {
            return false;
        }

        return false !== strpos($haystack, $needle, $temp);
    }

    /**
     * @param string $haystack
     * @param string $needle
     *
     * @return bool
     */
    protected function startsWith($haystack, $needle)
    {
        if (true === empty($needle)) {
            return true;
        }

        return false !== strrpos($haystack, $needle, -strlen($haystack));
    }

    /**
     * @param Tokens          $tokens
     * @param string[]|string $fqcn
     *
     * @return bool
     */
    protected function hasUseStatements(Tokens $tokens, $fqcn)
    {
        return null !== $this->getUseStatements($tokens, $fqcn);
    }

    /**
     * @param Tokens          $tokens
     * @param string[]|string $fqcn
     *
     * @return null|array
     */
    protected function getUseStatements(Tokens $tokens, $fqcn)
    {
        if (false === is_array($fqcn)) {
            $fqcn = explode('\\', $fqcn);
        }

        $sequence = array(array(T_USE));

        foreach ($fqcn as $component) {
            $sequence = array_merge(
                $sequence,
                array(array(T_STRING, $component), array(T_NS_SEPARATOR))
            );
        }

        $sequence[count($sequence) - 1] = ';';

        return $tokens->findSequence($sequence);
    }

    /**
     * @param Tokens          $tokens
     * @param string[]|string $oldFqcn
     * @param string          $newClassName
     *
     * @return bool
     */
    protected function renameUseStatements(Tokens $tokens, $oldFqcn, $newClassName)
    {
        $matchedTokens = $this->getUseStatements($tokens, $oldFqcn);

        if (null === $matchedTokens) {
            return false;
        }

        $matchedTokensIndexes = array_keys($matchedTokens);
        $classNameToken       = $matchedTokens[$matchedTokensIndexes[count($matchedTokensIndexes) - 2]];
        $classNameToken->setContent($newClassName);

        return true;
    }

    /**
     * @param Tokens   $tokens
     * @param string[] $fqcn
     */
    protected function addUseStatement(Tokens $tokens, array $fqcn)
    {
        if ($this->hasUseStatements($tokens, $fqcn)) {
            return;
        }

        $importUseIndexes = $tokens->getImportUseIndexes();

        if ( ! isset($importUseIndexes[0])) {
            return;
        }

        $fqcnTokens = array();

        foreach ($fqcn as $fqcnComponent) {
            $fqcnTokens[] = new Token(array(T_STRING, $fqcnComponent));
            $fqcnTokens[] = new Token(array(T_NS_SEPARATOR, '\\'));
        }

        array_pop($fqcnTokens);

        $tokens->insertAt(
            $importUseIndexes[0],
            array_merge(
                array(
                    new Token(array(T_USE, 'use')),
                    new Token(array(T_WHITESPACE, ' ')),
                ),
                $fqcnTokens,
                array(
                    new Token(';'),
                    new Token(array(T_WHITESPACE, PHP_EOL)),
                )
            )
        );
    }

    /**
     * @param Tokens          $tokens
     * @param string[]|string $fqcn
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

        return null !== $tokens->findSequence(array(
            array(T_CLASS),
            array(T_STRING),
            array(T_EXTENDS),
            array(T_STRING, array_pop($fqcn)),
        ));
    }

    /**
     * @param Tokens $tokens
     *
     * @return Token[]
     */
    protected function getComments(Tokens $tokens)
    {
        $comments = array();

        foreach ($tokens as $index => $token) {
            if (true === $token->isComment()) {
                $comments[$index] = $token;
            }
        }

        return $comments;
    }
}
