.PHONY: qa lint cs csf phpstan tests coverage-clover coverage-html

all:
	@awk 'BEGIN {FS = ":.*##"; printf "Usage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"}'
	@grep -h -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# QA

qa: cs phpstan ## Check code quality - static analysis

lint: ## Check PHP files syntax
	vendor/bin/parallel-lint --blame --colors src tests

cs: ## Check PHP files coding style
	vendor/bin/phpcs --cache=var/tmp/codesniffer.dat --standard=ruleset.xml --extensions=php,phtml --colors -nsp src tests

csf: ## Fix PHP files coding style
	vendor/bin/phpcbf --cache=var/tmp/codesniffer.dat --standard=ruleset.xml --extensions=php,phtml --colors -nsp src tests

phpstan: ## Analyse code with PHPStan
	vendor/bin/phpstan analyse -l 7 -c phpstan.src.neon src
	vendor/bin/phpstan analyse -l 1 -c phpstan.tests.neon tests

# Tests

tests: ## Run all tests
	vendor/bin/phpunit

coverage-clover: ## Generate code coverage in XML format
	phpdbg -qrr vendor/bin/phpunit --coverage-clover var/tmp/coverage.xml

coverage-html: ## Generate code coverage in HTML format
	phpdbg -qrr vendor/bin/phpunit --coverage-html var/tmp/coverage-html
