all:
	echo "I'm your helpful make!"

vendor: composer.json composer.lock
	composer install

qa: cs phpstan

lint: vendor
	vendor/bin/linter packages

cs: vendor
	vendor/bin/phpcs --cache=temp/codesniffer.dat --standard='ruleset.xml' --colors -nsp packages/**/src packages/**/tests

csf: vendor
	vendor/bin/phpcbf --cache=temp/codesniffer.dat --standard='ruleset.xml' --colors -nsp packages/**/src packages/**/tests

phpstan: vendor
	vendor/bin/phpstan analyse -l max -c phpstan.neon packages/**/src

meta-update: vendor
	vendor/bin/monorepo-builder merge

meta-validate: vendor
	vendor/bin/monorepo-builder validate

tests: vendor
	vendor/bin/tester -s -p php --colors 1 -C packages/**/tests/cases

coverage: vendor
	vendor/bin/tester -s -p phpdbg --colors 1 -C --coverage ./coverage.xml --coverage-src ./packages/**/src packages/**/tests/cases
