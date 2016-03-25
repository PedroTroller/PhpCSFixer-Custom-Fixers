<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class PropertyAssignAndReturnFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $index  = 0;
        $all    = array();

        do {
            $assign = $tokens->findSequence(array(
                array(T_VARIABLE, '$this'),
                array(T_OBJECT_OPERATOR),
                array(T_STRING),
                '=',
            ), $index);

            if (null !== $assign) {
                $keys        = array_keys($assign);
                $equals      = end($keys);
                $endline     = $tokens->getNextTokenOfKind($equals, array(';'));
                $valueTokens = array();

                for ($i = $tokens->getNextMeaningfulToken($equals); $i <= $endline; ++$i) {
                    $valueTokens[$i] = $tokens[$i];
                }

                $variable = $tokens[$keys[2]];
                $next     = $tokens->getNextMeaningfulToken($endline);

                if (T_RETURN === $tokens[$next]->getId()) {
                    $all[] = array(
                        'assign' => $assign,
                        'value'  => $valueTokens,
                        'return' => $tokens->findSequence(array(
                            array(T_VARIABLE, '$this'),
                            array(T_OBJECT_OPERATOR),
                            array(T_STRING, $variable->getContent()),
                            ';',
                        ), $next + 2),
                    );
                }

                $index = $next + 6;
            }
        } while (null !== $assign);

        foreach (array_reverse($all) as $item) {
            $assign = $item['assign'];
            $value  = $item['value'];
            $return = $item['return'];

            if (null === $return) {
                continue;
            }

            array_pop($assign);
            end($return);

            $range = array(key($assign), key($return));

            $newSet = array(
                new Token('return'),
                new Token(' '),
            );

            foreach ($assign as $token) {
                $newSet[] = new Token($token->getContent());
            }

            $newSet[] = new Token(' ');
            $newSet[] = new Token('=');
            $newSet[] = new Token(' ');

            foreach ($value as $token) {
                $newSet[] = new Token($token->getContent());
            }

            $tokens->clearRange($range[0], $range[1]);
            $tokens->insertAt($range[0], $newSet);
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Useless assignements and then returns properties MUST be done in a single line.';
    }
}
