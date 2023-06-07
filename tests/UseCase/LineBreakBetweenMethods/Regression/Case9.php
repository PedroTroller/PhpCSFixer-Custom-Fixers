<?php

declare(strict_types=1);

namespace tests\UseCase\LineBreakBetweenMethods\Regression;

use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use tests\UseCase;

/**
 * https://github.com/PedroTroller/PhpCSFixer-Custom-Fixers/issues/196.
 */
final class Case9 implements UseCase
{
    public function getFixers(): iterable
    {
        yield new LineBreakBetweenMethodArgumentsFixer();
    }

    public function getRawScript(): string
    {
        return <<<'PHP'
            <?php

            namespace App\Admin\Attributes\EasyAdmin;

            use App\Admin\Controller\AbstractCrudController;
            use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

            /**
             * This annotation will define which method of a CrudController is an EasyAdmin action
             *
             * @see AbstractCrudController
             */
            #[\Attribute(\Attribute::TARGET_METHOD)]
            readonly class EasyAction
            {
                /**
                 * @param string $label Action label, visible on index and other pages
                 * @param string|null $icon The icon css class
                 * @param string $detailAndEdit The detail and edit view css classes for the button
                 * @param string $index The index (list) view css classes for the button
                 * @param string|null $permission A permission to be checked for creating the action
                 * @param list<string> $pages list of {@link Crud} pages
                 */
                public function __construct(
                    public string $label = 'N/A',
                    public string|null $icon = null,
                    public string $detailAndEdit = '',
                    public string $index = '',
                    public string|null $permission = null,
                    public array $pages = [Crud::PAGE_INDEX, Crud::PAGE_DETAIL, Crud::PAGE_EDIT]
                ) {
                }
            }
            PHP;
    }

    public function getExpectation(): string
    {
        return $this->getRawScript();
    }

    public function getMinSupportedPhpVersion(): int
    {
        return 80200;
    }
}
