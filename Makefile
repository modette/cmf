.PHONY: qa lint cs csf phpstan tests coverage-clover coverage-html meta-update meta-validate

all:
	@$(MAKE) -pRrq -f $(lastword $(MAKEFILE_LIST)) : 2>/dev/null | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' | sort | egrep -v -e '^[^[:alnum:]]' -e '^$@$$' | xargs

vendor: composer.json composer.lock
	composer install

# QA

qa: cs phpstan

lint: vendor
	vendor/bin/parallel-lint --blame --colors packages/**/src packages/**/tests tests

cs: vendor
	vendor/bin/phpcs --cache=var/tmp/codesniffer.dat --standard=ruleset.xml --colors -nsp packages/**/src packages/**/tests tests

csf: vendor
	vendor/bin/phpcbf --cache=var/tmp/codesniffer.dat --standard=ruleset.xml --colors -nsp packages/**/src packages/**/tests tests

phpstan: vendor
	vendor/bin/phpstan analyse -l 7 -c phpstan.src.neon packages/**/src
	vendor/bin/phpstan analyse -l 1 -c phpstan.tests.neon packages/**/tests tests

# Tests

tests: vendor
	vendor/bin/phpunit

coverage-clover: vendor
	phpdbg -qrr vendor/bin/phpunit --coverage-clover var/tmp/coverage.xml

coverage-html: vendor
	phpdbg -qrr vendor/bin/phpunit --coverage-html var/tmp/coverage-html

# Meta

meta-update: vendor
	vendor/bin/monorepo-builder merge

meta-validate: vendor
	vendor/bin/monorepo-builder validate
