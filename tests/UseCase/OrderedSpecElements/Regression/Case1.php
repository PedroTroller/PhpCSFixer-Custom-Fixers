<?php

declare(strict_types=1);

namespace tests\UseCase\OrderedSpecElements\Regression;

use PedroTroller\CS\Fixer\Phpspec\OrderedSpecElementsFixer;
use tests\UseCase;

final class Case1 implements UseCase
{
    public function getFixers(): iterable
    {
        yield new OrderedSpecElementsFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
<?php

namespace spec\Domain\Model;

use Domain\Model;
use PhpSpec\ObjectBehavior;

class HotelSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Plazza Hotel', 145);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Model\Hotel::class);
    }

    function it_exposes_some_state()
    {
        $this->getName()->shouldReturn('Plazza Hotel');
        $this->getCapacity()->shouldReturn(145);
    }

    function it_is_mutable()
    {
        $this->setName('Ritz Hotel');
        $this->setCapacity(100);

        $this->getName()->shouldReturn('Ritz Hotel');
        $this->getCapacity()->shouldReturn(100);
    }
}
PHP;
    }

    public function getExpectation(): string
    {
        return <<<'PHP'
<?php

namespace spec\Domain\Model;

use Domain\Model;
use PhpSpec\ObjectBehavior;

class HotelSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Plazza Hotel', 145);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Model\Hotel::class);
    }

    function it_exposes_some_state()
    {
        $this->getName()->shouldReturn('Plazza Hotel');
        $this->getCapacity()->shouldReturn(145);
    }

    function it_is_mutable()
    {
        $this->setName('Ritz Hotel');
        $this->setCapacity(100);

        $this->getName()->shouldReturn('Ritz Hotel');
        $this->getCapacity()->shouldReturn(100);
    }
}
PHP;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
