<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\ClassNotation\OrderedWithGetterAndSetterFirstFixer;
use tests\UseCase;

final class OrderedWithGetterAndSetterFirst implements UseCase
{
    public function getFixers(): iterable
    {
        yield new OrderedWithGetterAndSetterFirstFixer();
    }

    public function getRawScript(): string
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

                public function enable()
                {
                    $this->enabled = true;
                }

                public function disable()
                {
                    $this->enabled = false;
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

                public function hasIdentifier()
                {
                    return null !== $this->identifier;
                }
            }
            PHP;
    }

    public function getExpectation(): string
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

                public function getIdentifier()
                {
                    return $this->identifier;
                }

                public function hasIdentifier()
                {
                    return null !== $this->identifier;
                }

                public function getName()
                {
                    return $this->name;
                }

                public function setName($name)
                {
                    $this->name = $name;
                }

                public function getFirstName()
                {
                    return $this->firstName;
                }

                public function setFirstName($firstName)
                {
                    $this->firstName = $firstName;
                }

                public function isEnabled()
                {
                    return $this->enabled;
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

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
