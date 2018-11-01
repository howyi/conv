test:
	@vendor/bin/phpunit
stan:
	@vendor/bin/phpstan analyse -l 7 -c phpstan.neon src

.PHONY: test stan
