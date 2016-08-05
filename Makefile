test:
	bin/phpspec run -fpretty
	bin/php-cs-fixer --diff --dry-run -v fix

fix:
	bin/php-cs-fixer --diff -v fix

docker-tests:
	docker-compose run php5.5 make test
	docker-compose run php5.6 make test
	docker-compose run php7.0 make test
