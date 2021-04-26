<?php

declare(strict_types=1);

namespace tests\UseCase\UselessComment\Regression;

use PedroTroller\CS\Fixer\Comment\UselessCommentFixer;
use tests\UseCase;

final class WithInterface implements UseCase
{
    public function getFixers(): iterable
    {
        yield new UselessCommentFixer();
    }

    public function getRawScript(): string
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

    public function getExpectation(): string
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

    public function getMinSupportedPhpVersion(): int
    {
        return 70000;
    }
}
