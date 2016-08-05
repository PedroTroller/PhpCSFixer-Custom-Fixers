<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

class MbstringFixer extends AbstractFixer
{
    public static $functions = array(
        'ereg_replace',
        'ereg',
        'eregi_replace',
        'eregi',
        'parse_str',
        'split',
        'stripos',
        'stristr',
        'strlen',
        'strpos',
        'strrchr',
        'strripos',
        'strrpos',
        'strstr',
        'strtolower',
        'strtoupper',
        'substr_count',
        'substr',
    );

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        if (false === extension_loaded('mbstring')) {
            return $content;
        }

        $tokens = Tokens::fromCode($content);

        foreach ($tokens as $token) {
            if (T_STRING !== $token->getId()) {
                continue;
            }

            if (false === in_array($token->getContent(), self::$functions)) {
                continue;
            }

            $token->setContent(sprintf('mb_%s', $token->getContent()));
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Multi-bytes string functions (mb_strtoupper, ...) MUST be used instead of traditional ones. Warning! This could change code behavior.';
    }
}
