<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\PhpspecFixer;
use tests\UseCase;

final class FuncSpec implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        $fixer = new PhpspecFixer();

        $fixer->configure([
            'instanceof' => [
                'Funk\Spec',
            ],
        ]);

        return $fixer;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
    {
        return <<<'SPEC'
<?php

namespace tests\integration\Application;

use App\Application\DirtinessRegistry;
use App\Domain\Model\Identifier;
use Assert\Assert;
use Funk\Spec;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DirtinessRegistryTest implements Spec
{
    /**
     * @var \App\Application\DirtinessRegistry
     */
    private $subject;

    public function __construct(ContainerInterface $container)
    {
        $this->subject = $container->get(DirtinessRegistry::class);
    }

    public function foo()
    {
        return 'bar';
    }

    function it_keeps_track_of_entity_dirtiness()
    {
        $uuid = Uuid::uuid4()->toString();

        Assert::that($this->subject->isDirty(Identifier::fromString($uuid)))->false();

        $this->subject->dirty(Identifier::fromString($uuid));
        Assert::that($this->subject->isDirty(Identifier::fromString($uuid)))->true();

        $this->subject->clean(Identifier::fromString($uuid));
        Assert::that($this->subject->isDirty(Identifier::fromString($uuid)))->false();
    }
}
SPEC;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
    {
        return <<<'SPEC'
<?php

namespace tests\integration\Application;

use App\Application\DirtinessRegistry;
use App\Domain\Model\Identifier;
use Assert\Assert;
use Funk\Spec;
use Ramsey\Uuid\Uuid;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DirtinessRegistryTest implements Spec
{
    /**
     * @var \App\Application\DirtinessRegistry
     */
    private $subject;

    public function __construct(ContainerInterface $container)
    {
        $this->subject = $container->get(DirtinessRegistry::class);
    }

    function it_keeps_track_of_entity_dirtiness()
    {
        $uuid = Uuid::uuid4()->toString();

        Assert::that($this->subject->isDirty(Identifier::fromString($uuid)))->false();

        $this->subject->dirty(Identifier::fromString($uuid));
        Assert::that($this->subject->isDirty(Identifier::fromString($uuid)))->true();

        $this->subject->clean(Identifier::fromString($uuid));
        Assert::that($this->subject->isDirty(Identifier::fromString($uuid)))->false();
    }

    public function foo()
    {
        return 'bar';
    }
}
SPEC;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }
}
