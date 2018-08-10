<?php

declare(strict_types=1);

namespace PedroTroller\CS\Fixer;

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
     * @param float $version
     * @param bool  $risky
     *
     * @return RuleSetFactory
     */
    public function php($version, $risky = false)
    {
        $config = [];

        switch (true) {
            case $version >= 7.1:
                $config = array_merge(['list_syntax' => ['syntax' => 'short']], $config);

                if ($risky) {
                    $config = array_merge(['@PHP71Migration:risky' => true], $config);
                }

                $config = array_merge(['@PHP71Migration' => true], $config);
                // no break
            case $version >= 7.0:
                if ($risky) {
                    $config = array_merge(['@PHP70Migration:risky' => true], $config);
                }

                $config = array_merge(['@PHP70Migration' => true], $config);
                // no break
            case $version >= 5.6:
                if ($risky) {
                    $config = array_merge(['@PHP56Migration:risky' => true], $config);
                }

                $config = array_merge(['@PHP56Migration' => true], $config);
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
     * @param string     $name
     * @param array|bool $config
     *
     * @return RuleSetFactory
     */
    public function enable($name, $config = true)
    {
        return self::create(array_merge(
            $this->rules,
            [$name => $config]
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
}
