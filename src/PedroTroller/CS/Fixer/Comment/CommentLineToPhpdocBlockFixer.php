<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\Comment;

use PedroTroller\CS\Fixer\AbstractFixer;
use PedroTroller\CS\Fixer\Priority;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class CommentLineToPhpdocBlockFixer extends AbstractFixer
{
    public function getSampleCode(): string
    {
        return <<<'PHP'
            <?php

            declare(strict_types=1);

            namespace App;

            final class TheClass
            {
                /**
                 * @var string
                 */
                private $name;

                // @var string | null
                private $value;

                /**
                 * @param string $name
                 */
                public function __construct($name)
                {
                    $this->name = $name;
                }

                // Get the name
                //
                // @return string
                public function getName()
                {
                    return $this->name;
                }

                // Get the value
                // @return null | string
                public function getValue()
                {
                    return $this->value;
                }

                // Set the value

                // @param string $value
                public function setValue($value)
                {
                    $this->value = $value;
                }
            }
            PHP;
    }

    public function getDocumentation(): string
    {
        return 'Classy elements (method, property, ...) comments MUST BE a PhpDoc block';
    }

    public function getPriority(): int
    {
        return Priority::after(SingleLineCommentStyleFixer::class);
    }

    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        $elements = $this->analyze($tokens)->getClassyElements();

        foreach (array_reverse($elements, true) as $index => $element) {
            $commentIndex = $index;

            do {
                $commentIndex = $tokens->getPrevNonWhitespace($commentIndex);
            } while ($tokens[$commentIndex]->isGivenKind([T_PRIVATE, T_PROTECTED, T_PUBLIC, T_ABSTRACT, T_STATIC]));

            if (false === $tokens[$commentIndex]->isComment()) {
                continue;
            }

            $comment = $tokens[$commentIndex];

            if (1 !== preg_match('/^ *\/\//', $comment->getContent())) {
                continue;
            }

            $comments        = explode("\n", $comment->getContent());
            $previous        = $commentIndex;
            $whitespaceIndex = null;

            do {
                --$previous;

                if ($tokens[$previous]->isComment()) {
                    $comments = array_merge(
                        $comments,
                        explode("\n", $tokens[$previous]->getContent())
                    );

                    $tokens->clearAt($previous);
                }

                if ($tokens[$previous]->isWhitespace()) {
                    $whitespaceIndex = $previous;
                    $tokens->clearAt($previous);
                }
            } while ($tokens[$previous]->isComment() || $tokens[$previous]->isWhitespace());

            $tokens[$commentIndex] = new Token([
                T_COMMENT,
                $this->formatComments(array_reverse($comments), $this->analyze($tokens)->getLineIndentation($index)),
            ]);

            if (null !== $whitespaceIndex) {
                $tokens[$whitespaceIndex] = new Token([
                    T_WHITESPACE,
                    "\n\n".$this->analyze($tokens)->getLineIndentation($index),
                ]);
            }
        }
    }

    /**
     * @param string[] $comments
     */
    private function formatComments(array $comments, string $indentation): string
    {
        $comments = array_map('trim', $comments);

        while (empty(current($comments))) {
            array_shift($comments);
        }

        while (empty(end($comments))) {
            array_pop($comments);
        }

        $comments = array_map(static fn ($comment) => rtrim($indentation.' * '.ltrim($comment, ' /')), $comments);

        $comments = implode("\n", $comments);
        $comments = trim($comments, " \n");

        return sprintf("/**\n%s %s\n%s */", $indentation, $comments, $indentation);
    }
}
