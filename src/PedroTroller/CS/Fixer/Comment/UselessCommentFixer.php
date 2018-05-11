<?php

namespace PedroTroller\CS\Fixer\Comment;

use PedroTroller\CS\Fixer\AbstractFixer;
use PedroTroller\CS\Fixer\TokensAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class UselessCommentFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDocumentation()
    {
        return 'Remove useless comments regarding the method definition';
    }

    /**
     * {@inheritdoc}
     */
    public function getSampleCode()
    {
        return <<<'PHP'
<?php

namespace Project\TheNamespace;

use App\Model;

class TheClass
{
    /**
     * @param Model\User $user
     */
    public function fun1(Model\User $user, Model\Address $address = null) {
        return;
    }

    /**
     * @param string $file
     */
    public function fun2($id, $file = null)
    {
        return true;
    }

    /**
     * Get the name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        $analyzer = new TokensAnalyzer($tokens);

        foreach ($analyzer->getClassyElements() as $index => $element) {
            if ('method' !== $element['type']) {
                continue;
            }

            $comment = $this->getFunctionComment($index, $tokens);

            if (null === $comment) {
                continue;
            }

            $uselessComments = $this->getUselessComments($index, $tokens);
            $commentText     = $tokens[$comment]->getContent();
            $lines           = explode("\n", $commentText);
            $changed         = false;
            $previous        = null;

            foreach ($lines as $index => $line) {
                if (false === array_key_exists($index, $lines)) {
                    continue;
                }

                $text = trim(ltrim(trim($line), '/*'));

                if (in_array($text, $uselessComments)) {
                    unset($lines[$index]);
                    $changed = true;

                    if (null !== $previous) {
                        $next = $index + 1;

                        if (array_key_exists($next, $lines) && empty(trim($lines[$previous], '/* ')) && empty(trim($lines[$next], '/* '))) {
                            unset($lines[$next]);
                        }
                    }
                } else {
                    $previous = $index;
                }
            }

            if (false === $changed) {
                continue;
            }

            $commentText = join("\n", $lines);

            if (empty(trim($commentText, "/* \n"))) {
                $tokens->clearAt($comment);
                $tokens->removeTrailingWhitespace($comment);
            } else {
                $tokens[$comment]->setContent($commentText);
            }
        }
    }

    /**
     * @param int $index
     *
     * @return null|int
     */
    private function getFunctionComment($index, Tokens $tokens)
    {
        $previous = $index;

        do {
            $previous = $tokens->getPrevNonWhitespace($previous);

            if (null === $previous) {
                return;
            }
        } while ($tokens[$previous]->isGivenKind([T_PUBLIC, T_PROTECTED, T_PRIVATE, T_ABSTRACT, T_STATIC]));

        if ($tokens[$previous]->isComment()) {
            return $previous;
        }
    }

    private function getUselessComments($index, Tokens $tokens)
    {
        $analyzer  = new TokensAnalyzer($tokens);
        $arguments = $analyzer->getMethodArguments($index) ?: [];
        $useless   = [];

        foreach ($arguments as $argument) {
            if (null === $argument['type']) {
                $useless[] = sprintf('@param %s', $argument['name']);
            } else {
                if ($argument['nullable']) {
                    $useless[] = sprintf('@param %s|null %s', $argument['type'], $argument['name']);
                    $useless[] = sprintf('@param null|%s %s', $argument['type'], $argument['name']);
                    $useless[] = sprintf('@param %s | null %s', $argument['type'], $argument['name']);
                    $useless[] = sprintf('@param null | %s %s', $argument['type'], $argument['name']);
                } else {
                    $useless[] = sprintf('@param %s %s', $argument['type'], $argument['name']);
                }
            }
        }

        $return = $analyzer->getReturnedType($index);

        if (null === $return) {
            $useless[] = '@return null';
        } elseif (false === is_array($return)) {
            $useless[] = sprintf('@return %s', $return);
        } else {
            $return = array_map(function ($value) { return null === $value ? 'null' : $value; }, $return);

            $useless[] = sprintf('@return %s', join('|', $return));
            $useless[] = sprintf('@return %s', join(' | ', $return));
            $useless[] = sprintf('@return %s', join('|', array_reverse($return)));
            $useless[] = sprintf('@return %s', join(' | ', array_reverse($return)));
        }

        return $useless;
    }
}
