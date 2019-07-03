<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\DeadCode;

use PedroTroller\CS\Fixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

final class UselessCodeAfterReturnFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDocumentation()
    {
        return 'Remove useless code after a returned value';
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

        $user->setName('foo');

        return $this;
    }

    /**
     * Get the name
     *
     * @return string|null
     */
    public function getName()
    {
        switch ($this->status) {
            case 1:
                return $this->name;
                break;
            default:
                return $this;
                return $this;
        }
    }

    /**
     * @return callable
     */
    public function buildCallable()
    {
        return function () { return true; return false; };
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        $returns  = $tokens->findGivenKind(T_RETURN);
        $analyzer = $this->analyze($tokens);

        foreach (array_reverse($returns, true) as $id => $return) {
            $start = $analyzer->getNextSemiColon($id);

            if (null === $start) {
                continue;
            }

            $possible = [$analyzer->endOfTheStatement($start)];

            foreach ($tokens->findGivenKind([T_CASE, T_DEFAULT, T_ENDSWITCH], $start) as $ends) {
                $possible = array_merge($possible, array_keys($ends));
            }

            $end = $tokens->getPrevMeaningfulToken(min($possible));

            if (($start + 1) > $end) {
                continue;
            }

            $tokens->clearRange(
                $start + 1,
                $end
            );
        }
    }
}
