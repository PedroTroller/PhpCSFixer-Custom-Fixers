<?php

namespace PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\AbstractFixer;
use SplFileInfo;
use Symfony\Component\Yaml\Yaml;

class YamlSymfonyServiceFileFixer extends AbstractFixer
{
    /**
     * @var string[]
     */
    private static $paths = array();

    /**
     * @var bool
     */
    private static $forcePrivate = true;

    /**
     * @var bool
     */
    private static $forceSorting = true;

    /**
     * @var string[]
     */
    private $properties = array(
        'public',
        'class',
        'lazy',
        'synthetic',
        'abstract',
        'parent',
        'decorates',
        'scope',
        'factory',
        'factoryClass',
        'factoryMethod',
        'factoryService',
        'autowire',
        'arguments',
        'calls',
        'tags',
    );

    /**
     * @param string $path
     */
    public static function addPath($path)
    {
        if (false === is_file($path) && false === is_dir($path)) {
            throw new \Exception(sprintf('File or directory %s not found', $path));
        }

        self::$paths[] = $path;
    }

    /**
     * @param bool $force
     */
    public static function forcePrivate($force)
    {
        self::$forcePrivate = $force;
    }

    /**
     * @param bool $force
     */
    public static function forceSorting($force)
    {
        self::$forceSorting = $force;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(SplFileInfo $file, $content)
    {
        $data   = Yaml::parse($content);
        $result = '';

        if (array_key_exists('imports', $data)) {
            $result = sprintf("%s%s\n", $result, Yaml::dump(array('imports' => $data['imports']), 2, 4));
            unset($data['imports']);
        }

        if (array_key_exists('parameters', $data)) {
            $result = sprintf("%s%s\n", $result, Yaml::dump(array('parameters' => $data['parameters']), 2, 4));
            unset($data['parameters']);
        }

        if (array_key_exists('services', $data)) {
            $services = $data['services'];

            foreach ($services as $name => $service) {
                $services[$name] = $this->patchService($service);
            }

            if (true === self::$forceSorting) {
                ksort($services);
            }

            $result = sprintf('%s%s', $result, preg_replace('/\n    ([^ ])/', "\n\n    $1", Yaml::dump(array('services' => $services), 4, 4)));
            unset($data['services']);
        }

        foreach ($data as $key => $value) {
            $result = sprintf('%s%s', $result, Yaml::dump(array($key => $value)));
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(SplFileInfo $file)
    {
        if ('yml' !== $file->getExtension()) {
            return false;
        }

        foreach (self::$paths as $path) {
            if (true === is_file($path) && realpath($path) === realpath($file->getPathname())) {
                return true;
            }

            if (is_dir($path)) {
                $expected = realpath($path);
                $current  = dirname(realpath($file->getPathname()));

                while (dirname($current) !== $current) {
                    if ($current === $expected) {
                        return true;
                    }

                    $current = dirname($current);
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * @param array $service
     *
     * @return array
     */
    private function patchService(array $service)
    {
        $result = array();

        if (true === self::$forcePrivate) {
            $service = array_merge(array('public' => false), $service);
        }

        foreach ($this->properties as $property) {
            if (false === array_key_exists($property, $service)) {
                continue;
            }

            $result[$property] = $service[$property];
            unset($service[$property]);
        }

        foreach ($service as $property => $value) {
            $result[$property] = $value;
        }

        return $result;
    }
}
