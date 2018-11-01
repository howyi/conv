test:
	@vendor/bin/phpunit
stan:
	@vendor/bin/phpstan analyse -l 7 src

.PHONY: test stan
