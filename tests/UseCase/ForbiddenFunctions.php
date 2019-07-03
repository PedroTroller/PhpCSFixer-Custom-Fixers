<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\CodingStyle\ForbiddenFunctionsFixer;
use tests\UseCase;

class ForbiddenFunctions implements UseCase
{
    /**
     * {@inheritdoc}
     */
    public function getFixer()
    {
        $fixer = new ForbiddenFunctionsFixer();

        $fixer->configure([
            'comment'   => 'NEIN NEIN NEIN !!!',
            'functions' => ['var_dump', 'dump'],
        ]);

        return $fixer;
    }

    /**
     * {@inheritdoc}
     */
    public function getRawScript()
    {
        return <<<'PHP'
<?php

class MyClass {
    public function fun()
    {
        var_dump('this is a var_dump');

        // OR

        dump('this is a var_dump');

        $this->dump($this);
    }

    public function dump($data)
    {
        parent::dump($this);

        return serialize($data);
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

class MyClass {
    public function fun()
    {
        var_dump('this is a var_dump'); // NEIN NEIN NEIN !!!

        // OR

        dump('this is a var_dump'); // NEIN NEIN NEIN !!!

        $this->dump($this);
    }

    public function dump($data)
    {
        parent::dump($this);

        return serialize($data);
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
