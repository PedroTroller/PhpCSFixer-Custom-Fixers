<?php

namespace PedroTroller\CS\Fixer;

use Symfony\CS\FixerInterface;

class CollectionFixer extends AbstractFixer
{
    /** @var FixerInterface[] */
    private $fixers;

    /**
     * @param FixerInterface[] $fixers
     */
    public function __construct(array $fixers)
    {
        foreach ($fixers as $fixer) {
            if (false === $fixer instanceof FixerInterface) {
                throw new \InvalidArgumentException(sprintf(
                    'Fixers have to be instances of %s, %s given',
                    'Symfony\CS\FixerInterface',
                    false === is_object($fixer) ? gettype($fixer) : get_class($fixer)
                ));
            }

            $this->fixers[] = $fixer;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        foreach ($this->getFixersOrderedByPrioriry() as $fixer) {
            $content = $fixer->fix($file, $content);
        }

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        $descriptions = array_map(function (FixerInterface $fixer) {
            return rtrim($fixer->getDescription(), '.');
        }, $this->getFixersOrderedByPrioriry());

        return implode(' AND ', $descriptions);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        $priorities = array_map(function (FixerInterface $fixer) {
            return $fixer->getPriority();
        }, $this->fixers);

        if (true === empty($priorities)) {
            return parent::getPriority();
        }

        return max($priorities);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        foreach ($this->getFixersOrderedByPrioriry() as $fixer) {
            if (false === $fixer->supports($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return FixerInterface[]
     */
    private function getFixersOrderedByPrioriry()
    {
        $fixers = $this->fixers;

        $priorities = array_map(function (FixerInterface $fixer) {
            return $fixer->getPriority();
        }, $fixers);

        array_multisort($priorities, SORT_ASC, SORT_NUMERIC, $fixers);

        return $fixers;
    }
}
