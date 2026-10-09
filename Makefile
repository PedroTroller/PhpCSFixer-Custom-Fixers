.DEFAULT_GOAL := help
.PHONY: help test readme readme-check lint fix

-include .env

PHP_VERSION ?= $(PHP_MIN_VERSION)
DEPENDENCIES ?= highest
UID ?= $(shell id -u)
GID ?= $(shell id -g)

export PHP_VERSION DEPENDENCIES
export HOST_UID := $(UID)
export HOST_GID := $(GID)

# FRESH=1 forces a fresh `composer update` of the host vendor/ before a dev target.
ifeq ($(FRESH),1)
.PHONY: vendor/autoload.php
endif

DEV  := docker compose -f compose.dev.yaml run --rm --build dev
TEST := docker compose -f compose.test.yaml run --rm --build test

help: ## Show this help
	@grep -hE '^[a-zA-Z_-]+:.*## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*## "} {printf "  %-14s %s\n", $$1, $$2}'

test: ## Run the test suite in a fresh image (PHP_VERSION=x.y, DEPENDENCIES=lowest)
	$(TEST) composer tests

readme: vendor/autoload.php ## Regenerate README.md from bin/doc.twig (FRESH=1 to re-resolve vendor)
	$(DEV) sh -c 'tmp=$$(mktemp) && php bin/doc > "$$tmp" && cat "$$tmp" > README.md'

readme-check: vendor/autoload.php ## Fail if README.md is not up to date (FRESH=1 to re-resolve vendor)
	$(DEV) sh -c 'tmp=$$(mktemp) && php bin/doc > "$$tmp" && diff -u README.md "$$tmp"'

lint: vendor/autoload.php ## Check coding standards, dry run as in CI
	$(DEV) env PHP_CS_FIXER_IGNORE_ENV=1 composer run php-cs-fixer

fix: vendor/autoload.php ## Fix coding standards in place
	$(DEV) env PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix -vvv --diff

vendor/autoload.php: composer.json .env
	$(DEV) composer update --no-interaction --no-progress
	@touch $@
