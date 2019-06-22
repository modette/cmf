.PHONY: qa phpstan tests

all:
	@$(MAKE) -pRrq -f $(lastword $(MAKEFILE_LIST)) : 2>/dev/null | awk -v RS= -F: '/^# File/,/^# Finished Make data base/ {if ($$1 !~ "^[#.]") {print $$1}}' | sort | egrep -v -e '^[^[:alnum:]]' -e '^$@$$' | xargs

vendor: composer.json composer.lock
	composer install

qa: phpstan

phpstan:
	vendor/bin/phpstan analyse -l max -c phpstan.neon src

tests: vendor
	vendor/bin/phpunit
