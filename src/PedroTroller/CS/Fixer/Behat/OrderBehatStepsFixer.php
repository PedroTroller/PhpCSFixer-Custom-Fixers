<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\Behat;

use PedroTroller\CS\Fixer\AbstractOrderedClassElementsFixer;
use PedroTroller\CS\Fixer\Priority;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\Tokenizer\Tokens;

final class OrderBehatStepsFixer extends AbstractOrderedClassElementsFixer implements ConfigurableFixerInterface
{
    public const ANNOTATION_PRIORITIES = [
        '@BeforeSuite',
        '@AfterSuite',
        '@BeforeScenario',
        '@AfterScenario',
        '@BeforeStep',
        '@AfterStep',
        '@Given',
        '@When',
        '@Then',
    ];

    public function getSampleConfigurations(): array
    {
        return [
            [],
            ['instanceof' => ['Behat\Behat\Context\Context']],
        ];
    }

    public function isCandidate(Tokens $tokens): bool
    {
        foreach ($this->configuration['instanceof'] as $parent) {
            if ($this->extendsClass($tokens, $parent)) {
                return true;
            }

            if ($this->implementsInterface($tokens, $parent)) {
                return true;
            }
        }

        return false;
    }

    public function getSampleCode(): string
    {
        return <<<'SPEC'
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
                 * @Then the response should be received
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
                 * @Given I am on the homepage
                 */
                public function iAmOnTheHomepage()
                {
                    // ...
                }

                /**
                 * @BeforeScenario
                 */
                public function reset()
                {
                    // ...
                }
            }
            SPEC;
    }

    public function getPriority(): int
    {
        return Priority::before(OrderedClassElementsFixer::class);
    }

    public function getDocumentation(): string
    {
        return 'Step definition methods in Behat contexts MUST BE ordered by annotation and method name.';
    }

    public function getConfigurationDefinition(): FixerConfigurationResolverInterface
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('instanceof', 'Parent class or interface of your behat context classes.'))
                ->setDefault(['Behat\Behat\Context\Context'])
                ->getOption(),
        ]);
    }

    protected function sortElements(array $elements): array
    {
        $ordered = [];

        foreach (self::ANNOTATION_PRIORITIES as $annotation) {
            $ordered[$annotation] = [];
        }

        foreach ($elements as $index => $element) {
            if ('method' !== $element['type']) {
                continue;
            }

            if (empty($element['comment'])) {
                continue;
            }

            foreach (self::ANNOTATION_PRIORITIES as $search) {
                $regex = "/^ *(\\/\\/|\\*).* {$search}( .+|$)/m";

                if (!preg_match($regex, $element['comment'])) {
                    continue;
                }

                $ordered[$search][$element['methodName']] = $element;
                unset($elements[$index]);

                continue 2;
            }
        }

        foreach ($ordered as $annotation => $methods) {
            ksort($ordered[$annotation]);
        }

        $result = [];

        foreach ($elements as $element) {
            if ('method' === $element['type'] && '__construct' !== $element['methodName']) {
                foreach ($ordered as $methods) {
                    foreach ($methods as $method) {
                        $result[] = $method;
                    }
                }
                $ordered = [];
            }

            $result[] = $element;
        }

        foreach ($ordered as $methods) {
            foreach ($methods as $method) {
                $result[] = $method;
            }
        }

        return $result;
    }
}
