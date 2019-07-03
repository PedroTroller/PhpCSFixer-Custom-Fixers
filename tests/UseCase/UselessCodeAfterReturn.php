<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\DeadCode\UselessCodeAfterReturnFixer;
use tests\UseCase;

class UselessCodeAfterReturn implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        return new UselessCodeAfterReturnFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
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
     * @return string|null
     */
    public function getName()
    {
        switch (empty($this->name)) {
            case true:
                return '';
            case false:
                return $this->name;
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
    public function getExpectation()
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
     * @return string|null
     */
    public function getName()
    {
        switch (empty($this->name)) {
            case true:
                return '';
            case false:
                return $this->name;
        }
    }

    /**
     * @return callable
     */
    public function buildCallable()
    {
        return function () { return true; };
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }
}
