###Force Multi-Bytes extention function usage

Force the usage of function implemented into the `mbstring` php extension.

For example : 

```php
var $foo = strtolower('FOO');
```

Becomes

```php
var $foo = mb_strtolower('FOO');
```

Configuration
-------------
```php
<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        //...
        'mbstring',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\MbstringFixer())
    ->finder($finder)
;
```

###Yaml service file dumper fixer

Automaticaly reformat Symfony DIC services Yaml files

For example : 

```yaml
imports:
    0:
        resource: services/prototype.yml
    1:
        resource: services/serialization.yml

parameters:
    param1: foo
    param2:
        bar: bar
        baz: baz

services:
    api.alice.provider:
        class: MyApp\Api\Alice\Provider
        arguments: [ '@translator', '@knp_dictionary.dictionary.event.page.page', '%kernel.cache_dir%/../fixtures' ]
        tags:
            - name: knp_rad_fixtures_load.provider

    api.asset.url_package:
        arguments:
            - '@request_stack'
            - '%base_url%'
        class: MyApp\Api\Templating\Asset\UrlPackage
        tags:
            0:
                name: kernel.event_listener
                event: kernel.controller
                method: onController
            1:
                name: kernel.event_listener
                event: kernel.response
                method: onResponse
```

Becomes

```yaml
imports:
    - { resource: services/prototype.yml }
    - { resource: services/serialization.yml }

parameters:
    param1: foo
    param2: { bar: bar, baz: baz }

services:

    api.alice.provider:
        class: MyApp\Api\Alice\Provider
        arguments:
            - '@translator'
            - '@knp_dictionary.dictionary.event.page.page'
            - '%kernel.cache_dir%/../fixtures'
        tags:
            - { name: knp_rad_fixtures_load.provider }

    api.asset.url_package:
        class: MyApp\Api\Templating\Asset\UrlPackage
        arguments:
            - '@request_stack'
            - '%base_url%'
        tags:
            - { name: kernel.event_listener, event: kernel.controller, method: onController }
            - { name: kernel.event_listener, event: kernel.response, method: onResponse }

```

Configuration
-------------
```php
<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
;

PedroTroller\CS\Fixer\Contrib\YamlSymfonyServiceFileFixer::addPath('app/config/services.yml'); // Register your files locations
PedroTroller\CS\Fixer\Contrib\YamlSymfonyServiceFileFixer::addPath('app/config/services/');

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        //...
        'yaml_symfony_service_file',
        //...
    ))
    ->addCustomFixer(new PedroTroller\CS\Fixer\Contrib\YamlSymfonyServiceFileFixer())
    ->finder($finder)
;
```
