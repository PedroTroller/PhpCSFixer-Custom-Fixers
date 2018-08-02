<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\Comment\CommentLineToPhpdocBlockFixer;
use tests\UseCase;

final class CommentLineToPhpdocBlock implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        return new CommentLineToPhpdocBlockFixer();
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace App;

final class TheClass
{
    /**
     * @var string
     */
    private $name;

    // @var string | null
    private $value;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    // Get the name
    //
    // @return string
    public function getName()
    {
        return $this->name;
    }

    // Get the value
    // @return null | string
    public function getValue()
    {
        return $this->value;
    }

    // Set the value

    // @param string $value
    public function setValue($value)
    {
        $this->value = $value;
    }

    // {@inheritdoc}
    public function update(array $data)
    {
        return [];
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpectation()
    {
        return <<<'PHP'
<?php

declare(strict_types=1);

namespace App;

final class TheClass
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string | null
     */
    private $value;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value
     * @return null | string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $data)
    {
        return [];
    }
}
PHP;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinSupportedPhpVersion()
    {
        return 0;
    }
}
