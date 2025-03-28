install:
	composer install

validate:
	composer validate
	composer dump-autoload

lint:
	composer exec --verbose phpcs -- src bin tests
	composer exec --verbose phpstan

lint-fix:
	composer exec --verbose phpcbf -- src bin tests

test:
	composer exec --verbose phpunit tests

#затестить
test-color:
	composer exec --verbose phpunit -- --colors=always tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

test-coverage-text:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text

test-coverage-html:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-html tests/coverage
