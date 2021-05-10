<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\Behat\OrderBehatStepsFixer;
use tests\UseCase;

final class Behat implements UseCase
{
    public function getFixers(): iterable
    {
        yield new OrderBehatStepsFixer();
    }

    public function getRawScript(): string
    {
        return <<<'CONTEXT'
            <?php

            declare(strict_types=1);

            namespace App\Tests\Behat;

            use Behat\Behat\Context\Context;
            use Symfony\Component\HttpFoundation\Request;
            use Symfony\Component\HttpFoundation\Response;
            use Symfony\Component\HttpKernel\KernelInterface;

            final class DemoContext implements Context
            {
                /**
                 * @var KernelInterface
                 */
                private $kernel;

                /**
                 * @var Response|null
                 */
                private $response;

                /**
                 * @Then the response should be received
                 */
                public function theResponseShouldBeReceived()
                {
                    // ...
                }

                /**
                 * @Given I am on the homepage
                 */
                public function iAmOnTheHomepage()
                {
                    // ...
                }

                public function __construct(KernelInterface $kernel)
                {
                    $this->kernel = $kernel;
                }

                /**
                 * @BeforeScenario
                 */
                public function resetDatabase()
                {
                    // ...
                }

                /**
                 * @BeforeScenario
                 * @AfterStep
                 */
                public function resetLogs()
                {
                    // ...
                }

                public function doSomething()
                {
                    // ...
                }

                /**
                 * @When a demo scenario sends a request to :path
                 */
                public function aDemoScenarioSendsARequestTo($path)
                {
                    // ...
                }

                /**
                 * @Given I have a user account
                 * @When I create a user account
                 */
                public function theResponseShouldBeReceived()
                {
                    // ...
                }

                /**
                 * @BeforeScenario
                 */
                public function resetDatabase()
                {
                    // ...
                }

                /**
                 * @BeforeScenario
                 * @AfterStep
                 */
                public function resetLogs()
                {
                    // ...
                }

                /**
                 * @BeforeScenario
                 * @Given I am anonymous
                 */
                public function iAmAnnon()
                {
                    // ...
                }
            }
            CONTEXT;
    }

    public function getExpectation(): string
    {
        return <<<'CONTEXT'
            <?php

            declare(strict_types=1);

            namespace App\Tests\Behat;

            use Behat\Behat\Context\Context;
            use Symfony\Component\HttpFoundation\Request;
            use Symfony\Component\HttpFoundation\Response;
            use Symfony\Component\HttpKernel\KernelInterface;

            final class DemoContext implements Context
            {
                /**
                 * @var KernelInterface
                 */
                private $kernel;

                /**
                 * @var Response|null
                 */
                private $response;

                public function __construct(KernelInterface $kernel)
                {
                    $this->kernel = $kernel;
                }

                /**
                 * @BeforeScenario
                 * @Given I am anonymous
                 */
                public function iAmAnnon()
                {
                    // ...
                }

                /**
                 * @BeforeScenario
                 */
                public function resetDatabase()
                {
                    // ...
                }

                /**
                 * @BeforeScenario
                 * @AfterStep
                 */
                public function resetLogs()
                {
                    // ...
                }

                /**
                 * @Given I am on the homepage
                 */
                public function iAmOnTheHomepage()
                {
                    // ...
                }

                /**
                 * @Given I have a user account
                 * @When I create a user account
                 */
                public function theResponseShouldBeReceived()
                {
                    // ...
                }

                /**
                 * @When a demo scenario sends a request to :path
                 */
                public function aDemoScenarioSendsARequestTo($path)
                {
                    // ...
                }

                /**
                 * @Then the response should be received
                 */
                public function theResponseShouldBeReceived()
                {
                    // ...
                }

                public function doSomething()
                {
                    // ...
                }
            }
            CONTEXT;
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
