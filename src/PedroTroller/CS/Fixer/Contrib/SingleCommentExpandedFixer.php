<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

class SingleCommentExpandedFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private static $expanded = array('@var');

    /**
     * @var string
     */
    private $regex = '/( *)\/[*]{1,2} %s (.+) \*\//';

    /**
     * @param string[] $expanded
     */
    public static function setExpandedComments(array $expanded)
    {
        self::$expanded = $expanded;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        if ('php' !== $file->getExtension()) {
            return $content;
        }

        $tokens = Tokens::fromCode($content);

        foreach ($this->getComments($tokens) as $index => $token) {
            $space = '';
            if (null !== $prev = $tokens->getPrevTokenOfKind($index, array(array(T_WHITESPACE)))) {
                $spaces = explode("\n", $tokens[$prev]->getContent());
                $space  = end($spaces);
            }
            foreach (self::$expanded as $variable) {
                $regex   = sprintf($this->regex, $variable);
                $replace = sprintf("/**\n$space * %s $2\n$space */", $variable);
                $comment = preg_replace($regex, $replace, $token->getContent());

                $token->setContent($comment);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Docblocks with just one comment MUST be expanded into multilines.';
    }
}
