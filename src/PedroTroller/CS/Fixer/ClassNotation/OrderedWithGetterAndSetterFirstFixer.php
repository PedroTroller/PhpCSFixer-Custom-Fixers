<?php

namespace PedroTroller\CS\Fixer\ClassNotation;

use PedroTroller\CS\Fixer\AbstractOrderedClassElementsFixer;
use PedroTroller\CS\Fixer\Priority;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

final class OrderedWithGetterAndSetterFirstFixer extends AbstractOrderedClassElementsFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return Priority::before(OrderedClassElementsFixer::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentation()
    {
        return 'Class/interface/trait methods MUST BE ordered (getter and setters at the end, ordered following arguments order).';
    }

    /**
     * {@inheritdoc}
     */
    public function getSampleCode()
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

    /**
     * {@inheritdoc}
     */
    protected function sortElements(array $elements)
    {
        $methods  = $this->getMethodsNames($elements);
        $portions = [];

        foreach ($methods as $method) {
            foreach ($elements as $index => $element) {
                if ('method' !== $element['type']) {
                    continue;
                }

                if (\in_array($element['methodName'], $methods)) {
                    $portions[array_search($element['methodName'], $methods)] = $element;
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

    private function getMethodsNames(array $elements)
    {
        $methods = [];

        foreach ($this->getPropertiesNames($elements) as $name) {
            $methods[] = sprintf('get%s', ucfirst($name));
            $methods[] = sprintf('is%s', ucfirst($name));
            $methods[] = sprintf('set%s', ucfirst($name));
        }

        return $methods;
    }

    private function getPropertiesNames(array $elements)
    {
        $properties = array_filter($elements, function ($element) {
            return 'property' === $element['type'];
        });

        return array_map(function ($element) {
            return ltrim($element['propertyName'], '$');
        }, $properties);
    }
}
