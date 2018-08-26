all:
	echo "I'm your helpful make!"

vendor: composer.json composer.lock
	composer install

qa: phpstan

phpstan:
	vendor/bin/phpstan analyse -l max -c phpstan.neon src

tests: vendor
	vendor/bin/tester -s -p php --colors 1 -C tests/cases
