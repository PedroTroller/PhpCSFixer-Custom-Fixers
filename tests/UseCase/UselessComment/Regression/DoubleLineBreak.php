<?php

declare(strict_types=1);

namespace tests\UseCase\UselessComment\Regression;

use PedroTroller\CS\Fixer\Comment\UselessCommentFixer;
use tests\UseCase;

class DoubleLineBreak implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        return new UselessCommentFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
    {
        return <<<'PHP'
<?php

class MyClass {

    /**
     * @var User
     */
    private $user;

    /**
     * Set the user, this user should nianiania
     *
     * @param User $user
     *
     * @return MyClass
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
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

class MyClass {

    /**
     * @var User
     */
    private $user;

    /**
     * Set the user, this user should nianiania
     *
     * @return MyClass
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
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
