.PHONY: qa phpstan tests

all:
	@awk 'BEGIN {FS = ":.*##"; printf "Usage:\n  make \033[36m<target>\033[0m\n\nTargets:\n"}'
	@grep -h -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# QA

qa: phpstan ## Check code quality - static analysis

phpstan: ## Analyse code with PHPStan
	vendor/bin/phpstan analyse -l 7 -c phpstan.neon src

# Tests

tests: ## Run all tests
	vendor/bin/phpunit
