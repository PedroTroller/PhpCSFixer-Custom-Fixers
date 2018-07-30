<?php

declare(strict_types=1);

namespace tests\UseCase\UselessComment\Regression;

use PedroTroller\CS\Fixer\Comment\UselessCommentFixer;
use tests\UseCase;

final class WithInterface implements UseCase
{
    // @return FixerInterface
    public function getFixer()
    {
        return new UselessCommentFixer();
    }

    // @return string
    public function getRawScript()
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Token;

use DateTimeInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function lifetime(array $payloads, DateTimeInterface $expiredAt): string;

    /**
     * @return string
     */
    public function disposable(array $payloads, DateTimeInterface $expiredAt = null): string;
}
PHP;
    }

    // @return string
    public function getExpectation()
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Token;

use DateTimeInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
interface TokenGeneratorInterface
{
    public function lifetime(array $payloads, DateTimeInterface $expiredAt): string;

    public function disposable(array $payloads, DateTimeInterface $expiredAt = null): string;
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 70000;
    }
}
