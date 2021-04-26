<?php

declare(strict_types=1);

namespace tests\UseCase\UselessComment\Regression;

use PedroTroller\CS\Fixer\Comment\UselessCommentFixer;
use tests\UseCase;

final class DoubleLineBreak implements UseCase
{
    public function getFixers(): iterable
    {
        yield new UselessCommentFixer();
    }

    public function getRawScript(): string
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

    public function getExpectation(): string
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

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
