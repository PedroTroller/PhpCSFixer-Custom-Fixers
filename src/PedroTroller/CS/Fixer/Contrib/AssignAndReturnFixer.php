<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\CollectionFixer;

class AssignAndReturnFixer extends CollectionFixer
{
    public function __construct()
    {
        parent::__construct(array(
            new VariableAssignAndReturnFixer(),
            new PropertyAssignAndReturnFixer(),
        ));
    }
}
