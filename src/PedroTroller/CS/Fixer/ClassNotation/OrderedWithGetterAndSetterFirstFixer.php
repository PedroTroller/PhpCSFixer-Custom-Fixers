<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer\ClassNotation;

use PedroTroller\CS\Fixer\AbstractOrderedClassElementsFixer;
use PedroTroller\CS\Fixer\Priority;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class OrderedWithGetterAndSetterFirstFixer extends AbstractOrderedClassElementsFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    public function getPriority(): int
    {
        return Priority::before(OrderedClassElementsFixer::class);
    }

    public function getDocumentation(): string
    {
        return 'Class/interface/trait methods MUST BE ordered (accessors at the beginning of the class, ordered following properties order).';
    }

    public function getSampleCode(): string
    {
        return <<<'PHP'
            <?php

            namespace App\Model;

            class User
            {
                /**
                 * @var string
                 */
                private $identifier;

                /**
                 * @var string
                 */
                private $name;

                /**
                 * @var string
                 */
                private $firstName;

                /**
                 * @var string
                 */
                private $enabled;

                public function __construct(array $data)
                {
                    foreach ($data as $key => $value) {
                        $this->$key = $value;
                    }
                }

                public function setFirstName($firstName)
                {
                    $this->firstName = $firstName;
                }

                public function setName($name)
                {
                    $this->name = $name;
                }

                public function isEnabled()
                {
                    return $this->enabled;
                }

                public function getName()
                {
                    return $this->name;
                }

                public function getIdentifier()
                {
                    return $this->identifier;
                }

                public function getFirstName()
                {
                    return $this->firstName;
                }

                public function enable()
                {
                    $this->enabled = true;
                }

                public function disable()
                {
                    $this->enabled = false;
                }
            }
            PHP;
    }

    protected function sortElements(array $elements): array
    {
        $methods  = $this->getMethodsNames($elements);
        $portions = [];

        foreach ($methods as $method) {
            foreach ($elements as $index => $element) {
                if ('method' !== $element['type']) {
                    continue;
                }

                if (\in_array($element['methodName'], $methods, true)) {
                    $portions[array_search($element['methodName'], $methods, true)] = $element;
                    unset($elements[$index]);
                }
            }
        }

        ksort($portions);

        $result = [];

        foreach ($elements as $element) {
            if ('method' !== $element['type']) {
                $result[] = $element;
            }
        }

        foreach ($portions as $portion) {
            $result[] = $portion;
        }

        foreach ($elements as $element) {
            if ('method' === $element['type']) {
                $result[] = $element;
            }
        }

        return $result;
    }

    private function getMethodsNames(array $elements): array
    {
        $methods = [];

        foreach ($this->getPropertiesNames($elements) as $name) {
            $methods[] = sprintf('get%s', ucfirst($name));
            $methods[] = sprintf('is%s', ucfirst($name));
            $methods[] = sprintf('has%s', ucfirst($name));
            $methods[] = lcfirst($name);
            $methods[] = sprintf('set%s', ucfirst($name));
        }

        return $methods;
    }

    private function getPropertiesNames(array $elements): array
    {
        $properties = array_filter($elements, static fn ($element) => 'property' === $element['type']);

        return array_map(static fn ($element) => ltrim($element['propertyName'], '$'), $properties);
    }
}
