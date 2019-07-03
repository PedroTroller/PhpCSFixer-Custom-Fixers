<?php

declare(strict_types=1);

namespace tests\UseCase\OrderedWithGetterAndSetterFirst\Regression;

use PedroTroller\CS\Fixer\ClassNotation\OrderedWithGetterAndSetterFirstFixer;
use tests\UseCase;

class Case2 implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        return new OrderedWithGetterAndSetterFirstFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
    {
        return <<<'PHP'
<?php

namespace spec\Domain\Agenda\Slot;

use DateTimeImmutable;
use DateTimezone;
use Domain\Agenda\Slot;
use PhpSpec\ObjectBehavior;

class FreeSpec extends ObjectBehavior
{
    function let()
    {
        $start = new DateTimeImmutable('4:00pm 26-06-2018', new DateTimezone('Europe/London'));
        $end   = new DateTimeImmutable('4:30pm 26-06-2018', new DateTimezone('Europe/London'));

        $this->beConstructedWith($start, $end, 'Europe/London');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Slot\Free::class);
        $this->shouldImplement(Slot::class);
    }

    function it_has_an_unique_identifier_for_its_period_of_time()
    {
        $this->getIdentifier()->shouldReturn('20180626-1600-1630');
    }

    function it_has_a_start_date()
    {
        $this
            ->getStartAt()
            ->shouldBeLike(new DateTimeImmutable('4:00pm 26-06-2018', new DateTimezone('Europe/London')))
        ;
    }

    function it_has_a_end_date()
    {
        $this
            ->getEndAt()
            ->shouldbeLike(new DateTimeImmutable('4:30pm 26-06-2018', new DateTimezone('Europe/London')))
        ;
    }

    function it_is_free()
    {
        $this->isFree()->shouldReturn(true);
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
    {
        return $this->getRawScript();
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }
}
