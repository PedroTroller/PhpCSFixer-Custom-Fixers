<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\AbstractFixer;

class SingleCommentInlinerFixer extends AbstractFixer
{
    /** @var string[] */
    private static $inlined = array('var');

    /** @var string */
    private $regex = '/( *)\/[*]{1,2}\n( *)[*]{1,2} @%s (.+)\n( *)\*\//';

    /**
     * @param string[] $inlined
     */
    public static function setInlinedComments(array $inlined)
    {
        self::$inlined = $inlined;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        if ('php' !== $file->getExtension()) {
            return $content;
        }

        foreach (self::$inlined as $variable) {
            $regex   = sprintf($this->regex, $variable);
            $replace = sprintf('$1/** @%s $3 */', $variable);
            $content = preg_replace($regex, $replace, $content);
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Doc blocks with just one comment MUST be written in a single line.';
    }
}
