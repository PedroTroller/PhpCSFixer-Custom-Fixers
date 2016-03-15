<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

class SingleCommentCollapsedFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private static $collapsed = array('@var');

    /**
     * @var string
     */
    private $regex = '/( *)\/[*]{1,2}\n( *)[*]{1,2} %s (.+)\n( *)\*\//';

    /**
     * @param string[] $collapsed
     */
    public static function setCollapsedComments(array $collapsed)
    {
        self::$collapsed = $collapsed;
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
            foreach (self::$collapsed as $variable) {
                $regex   = sprintf($this->regex, $variable);
                $replace = sprintf('$1/** %s $3 */', $variable);
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
        return 'Docblocks with just one comment MUST be written in a single line.';
    }
}
