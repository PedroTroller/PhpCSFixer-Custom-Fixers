<?php

declare(strict_types=1);

namespace tests\UseCase;

use PedroTroller\CS\Fixer\CodingStyle\ForbiddenFunctionsFixer;
use tests\UseCase;

final class ForbiddenFunctions implements UseCase
{
    public function getFixers(): iterable
    {
        $fixer = new ForbiddenFunctionsFixer();

        $fixer->configure([
            'comment'   => 'NEIN NEIN NEIN !!!',
            'functions' => ['var_dump', 'dump'],
        ]);

        yield $fixer;
    }

    public function getRawScript(): string
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

    public function getExpectation(): string
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

    public function getMinSupportedPhpVersion(): int
    {
        return 0;
    }
}
