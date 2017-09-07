<?php

namespace PedroTroller\CS\Fixer\Comment;

use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;

class SingleLineCommentFixer extends AbstractFixer implements ConfigurationDefinitionFixerInterface
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
    public function isCandidate(Tokens $tokens)
    {
        return true;
    }

    public function getSampleConfigurations(): array
    {
        return [
            ['action' => 'expanded'],
            ['action' => 'collapsed'],
            ['action' => 'collapsed', 'types' => ['@var', '@return']],
        ];
    }

    public function getDocumentation(): string
    {
        return 'PHPSpec spec functions MUST NOT have a public scope.';
    }

    public function getSampleCode(): string
    {
        return <<<SPEC
<?php

namespace Project\TheNamespace;

class TheClass
{
    /** @var string */
    private \$prop1;

    /**
     * @var string
     */
    private \$prop1;

    /**
     * @return null
     */
    public function fun1(\$file) {
        return;
    }

    /** @return null */
    public function fun2(\$file) {
        return;
    }
}
SPEC;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens)
    {
        $this->{$this->configuration['action'].'Comment'}($tokens);
    }

    private function collapsedComment(Tokens $tokens)
    {
        foreach ($this->getComments($tokens) as $index => $token) {
            foreach ($this->configuration['types'] as $variable) {
                $regex   = sprintf($this->collapseRegex, $variable);
                $replace = sprintf('$1/** %s $3 */', $variable);
                $comment = preg_replace($regex, $replace, $token->getContent());
                $token->setContent($comment);
            }
        }
    }

    private function expandedComment(Tokens $tokens)
    {
        foreach ($this->getComments($tokens) as $index => $token) {
            $space = '';
            if (null !== $prev = $tokens->getPrevTokenOfKind($index, [[T_WHITESPACE]])) {
                $spaces = explode("\n", $tokens[$prev]->getContent());
                $space  = end($spaces);
            }

            foreach ($this->configuration['types'] as $variable) {
                $regex   = sprintf($this->expandRegex, $variable);
                $replace = sprintf("/**\n%s * %s $2\n%s */", $space, $variable, $space);
                $comment = preg_replace($regex, $replace, $token->getContent());
                $token->setContent($comment);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('action', 'Collapse or expand the single line comments'))
                ->setAllowedValues(['expanded', 'collapsed'])
                ->setDefault('collapsed')
                ->getOption(),
            (new FixerOptionBuilder('types', 'Collapse or expand the single line comments'))
                ->setDefault(['@var'])
                ->getOption(),
        ]);
    }
}
