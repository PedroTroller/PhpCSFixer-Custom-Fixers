<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

use PhpCsFixer\RuleSet;

final class RuleSetFactory
{
    /**
     * @var array[]
     */
    private $rules;

    public function __construct(array $rules = [])
    {
        $this->rules = $rules;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        $rules = $this->rules;

        ksort($rules);

        return $rules;
    }

    /**
     * @return RuleSetFactory
     */
    public static function create(array $rules = [])
    {
        return new self($rules);
    }

    /**
     * @return RuleSetFactory
     */
    public function psr0()
    {
        return self::create(array_merge(
            $this->rules,
            ['@psr0' => true]
        ));
    }

    /**
     * @return RuleSetFactory
     */
    public function psr1()
    {
        return self::create(array_merge(
            $this->rules,
            ['@psr1' => true]
        ));
    }

    /**
     * @return RuleSetFactory
     */
    public function psr2()
    {
        return self::create(array_merge(
            $this->rules,
            ['@psr2' => true]
        ));
    }

    /**
     * @return RuleSetFactory
     */
    public function psr4()
    {
        return self::create(array_merge(
            $this->rules,
            ['@psr4' => true]
        ));
    }

    /**
     * @param bool $risky
     *
     * @return RuleSetFactory
     */
    public function symfony($risky = false)
    {
        $rules = ['@Symfony' => true];

        if ($risky) {
            $rules['@Symfony:risky'] = true;
        }

        return self::create(array_merge(
            $this->rules,
            $rules
        ));
    }

    /**
     * @return RuleSetFactory
     */
    public function doctrineAnnotation()
    {
        return self::create(array_merge(
            $this->rules,
            ['@DoctrineAnnotation' => true]
        ));
    }

    /**
     * @param float $version
     * @param bool  $risky
     *
     * @return RuleSetFactory
     */
    public function php($version, $risky = false)
    {
        $config = $this->migration('php', $version, $risky)->getRules();

        switch (true) {
            case $version >= 7.1:
                $config = array_merge(['list_syntax' => ['syntax' => 'short']], $config);
                // no break
            case $version >= 5.4:
                $config = array_merge(['array_syntax' => ['syntax' => 'short']], $config);
        }

        $config = array_merge(['list_syntax' => ['syntax' => 'long']], $config);
        $config = array_merge(['array_syntax' => ['syntax' => 'long']], $config);

        return self::create(array_merge(
            $this->rules,
            $config
        ));
    }

    /**
     * @param float $version
     * @param bool  $risky
     *
     * @return RuleSetFactory
     */
    public function phpUnit($version, $risky = false)
    {
        return $this->migration('phpunit', $version, $risky);
    }

    /**
     * @param bool $risky
     *
     * @return RuleSetFactory
     */
    public function pedrotroller($risky = false)
    {
        $rules = [];

        foreach (new Fixers() as $fixer) {
            if ($fixer->isDeprecated()) {
                continue;
            }

            $rules[$fixer->getName()] = true;
        }

        ksort($rules);

        return self::create(array_merge(
            $this->rules,
            $rules
        ));
    }

    /**
     * @param string $name
     *
     * @return RuleSetFactory
     */
    public function enable($name, array $config = null)
    {
        return self::create(array_merge(
            $this->rules,
            [$name => \is_array($config) ? $config : true]
        ));
    }

    /**
     * @param string $name
     *
     * @return RuleSetFactory
     */
    public function disable($name)
    {
        return self::create(array_merge(
            $this->rules,
            [$name => false]
        ));
    }

    /**
     * @param string $package
     * @param float  $version
     * @param bool   $risky
     *
     * @return RuleSetFactory
     */
    private function migration($package, $version, $risky)
    {
        $rules = (new RuleSet())->getSetDefinitionNames();
        $rules = array_combine($rules, $rules);

        $rules = array_map(function ($name) {
            $matches = [];

            preg_match('/^@([A-Za-z]+)(\d+)Migration(:risky|)$/', $name, $matches);

            return $matches;
        }, $rules);

        $rules = array_filter($rules);

        $rules = array_filter($rules, function ($versionAndRisky) use ($package) {
            list($rule, $rulePackage, $ruleVersion, $ruleRisky) = $versionAndRisky;

            return strtoupper($package) === strtoupper($rulePackage);
        });

        $rules = array_filter($rules, function ($versionAndRisky) use ($version) {
            list($rule, $rulePackage, $ruleVersion, $ruleRisky) = $versionAndRisky;

            return ((float) $ruleVersion / 10) <= $version;
        });

        $rules = array_filter($rules, function ($versionAndRisky) use ($risky) {
            list($rule, $rulePackage, $ruleVersion, $ruleRisky) = $versionAndRisky;

            if ($risky) {
                return true;
            }

            return empty($ruleRisky);
        });

        return self::create(array_merge(
            $this->rules,
            array_map(function () { return true; }, $rules)
        ));
    }
}
