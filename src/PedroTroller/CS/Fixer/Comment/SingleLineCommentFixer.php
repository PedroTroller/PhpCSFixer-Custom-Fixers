<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\Comment;

use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Fixer\Comment\SingleLineCommentStyleFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class SingleLineCommentFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
{
    /**
     * @var string
     */
    private $collapseRegex = '/( *)\/[*]{1,2}\n( *)[*]{1,2} %s (.+)\n( *)\*\//';

    /**
     * @var string
     */
    private $expandRegex = '/( *)\/[*]{1,2} %s (.+) \*\//';

    /**
     * {@inheritdoc}
     */
    public function getSampleConfigurations()
    {
        return [
            ['action' => 'expanded'],
            ['action' => 'collapsed'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentation()
    {
        return 'PHP comments on a single line MUST BE reduced or expanded (according to the specified strategy)';
    }

    /**
     * {@inheritdoc}
     */
    public function getSampleCode()
    {
        return <<<'PHP'
<?php

namespace Project\TheNamespace;

class TheClass
{
    /** @var string */
    private $prop1;

    /**
     * @var string
     */
    private $prop1;

    /**
     * @return null
     */
    public function fun1($file) {
        return;
    }

    /** @return null */
    public function fun2($file) {
        return;
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeprecated()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeprecationReplacement()
    {
        return (new SingleLineCommentStyleFixer())->getName();
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        $this->{$this->configuration['action'].'Comment'}($tokens);
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('action', 'The strategy to be applied'))
                ->setAllowedValues(['expanded', 'collapsed'])
                ->setDefault('expanded')
                ->getOption(),
            (new FixerOptionBuilder('types', 'The types of comments on which the strategy should be applied'))
                ->setDefault(['@var', '@return', '@param'])
                ->getOption(),
        ]);
    }

    private function expandedComment(Tokens $tokens): void
    {
        foreach ($this->getComments($tokens) as $index => $token) {
            $space = '';
            if (null !== $prev = $tokens->getPrevTokenOfKind($index, [[T_WHITESPACE]])) {
                $spaces = explode("\n", $tokens[$prev]->getContent());
                $space  = end($spaces);
            }

            foreach ($this->configuration['types'] as $variable) {
                $regex          = sprintf($this->expandRegex, $variable);
                $replace        = sprintf("/**\n%s * %s $2\n%s */", $space, $variable, $space);
                $comment        = preg_replace($regex, $replace, $token->getContent());
                $tokens[$index] = new Token([T_COMMENT, $comment]);
            }
        }
    }

    private function collapsedComment(Tokens $tokens): void
    {
        foreach ($this->getComments($tokens) as $index => $token) {
            foreach ($this->configuration['types'] as $variable) {
                $regex          = sprintf($this->collapseRegex, $variable);
                $replace        = sprintf('$1/** %s $3 */', $variable);
                $comment        = preg_replace($regex, $replace, $token->getContent());
                $tokens[$index] = new Token([T_COMMENT, $comment]);
            }
        }
    }
}
