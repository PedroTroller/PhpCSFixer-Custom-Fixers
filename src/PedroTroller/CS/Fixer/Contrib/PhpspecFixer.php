<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\CollectionFixer;

class PhpspecFixer extends CollectionFixer
{
    public function __construct()
    {
        parent::__construct(array(
            new PhpspecScenarioNameUnderscorecaseFixer(),
            new PhpspecScenarioScopeFixer(),
        ));
    }
}
