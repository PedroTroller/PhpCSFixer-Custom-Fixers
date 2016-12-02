<?php

namespace spec\PedroTroller\CS\Fixer\Contrib;

use PedroTroller\CS\Fixer\Contrib\YamlSymfonyServiceFileFixer;
use PhpSpec\ObjectBehavior;
use SplFileInfo;

class YamlSymfonyServiceFileFixerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('PedroTroller\CS\Fixer\Contrib\YamlSymfonyServiceFileFixer');
    }

    function it_formats_yaml(SplFileInfo $file)
    {
        $yaml = <<<YAML
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


    api.asset.url_package:
        public: true
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
    api.alice.provider:
        class: MyApp\Api\Alice\Provider
        arguments: [ '@translator', '@knp_dictionary.dictionary.event.page.page', '%kernel.cache_dir%/../fixtures' ]
        tags:
            - name: knp_rad_fixtures_load.provider
YAML;

        $expected = <<<YAML
imports:
    - { resource: services/prototype.yml }
    - { resource: services/serialization.yml }

parameters:
    param1: foo
    param2: { bar: bar, baz: baz }

services:

    api.alice.provider:
        public: false
        class: MyApp\Api\Alice\Provider
        arguments:
            - '@translator'
            - '@knp_dictionary.dictionary.event.page.page'
            - '%kernel.cache_dir%/../fixtures'
        tags:
            - { name: knp_rad_fixtures_load.provider }

    api.asset.url_package:
        public: true
        class: MyApp\Api\Templating\Asset\UrlPackage
        arguments:
            - '@request_stack'
            - '%base_url%'
        tags:
            - { name: kernel.event_listener, event: kernel.controller, method: onController }
            - { name: kernel.event_listener, event: kernel.response, method: onResponse }

YAML;

        $this->fix($file, $yaml)->shouldReturn($expected);
    }

    function it_is_possible_to_disable_private_service_autosetting(SplFileInfo $file)
    {
        $yaml = <<<YAML
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
        public: true
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
    service.import:
        class: MyApp\Import
        arguments: ['%mapping%']
    service.export:
        class: MyApp\Export
        arguments: ['%mapping%']

    service.chain:
        class: MyApp\Chain
        arguments:
            import: "@service.import"
            export: "@service.export"
YAML;

        $expected = <<<YAML
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
        public: true
        class: MyApp\Api\Templating\Asset\UrlPackage
        arguments:
            - '@request_stack'
            - '%base_url%'
        tags:
            - { name: kernel.event_listener, event: kernel.controller, method: onController }
            - { name: kernel.event_listener, event: kernel.response, method: onResponse }

    service.chain:
        class: MyApp\Chain
        arguments:
            import: '@service.import'
            export: '@service.export'

    service.export:
        class: MyApp\Export
        arguments:
            - '%mapping%'

    service.import:
        class: MyApp\Import
        arguments:
            - '%mapping%'

YAML;
        YamlSymfonyServiceFileFixer::forcePrivate(false);

        $this->fix($file, $yaml)->shouldReturn($expected);
    }
}
