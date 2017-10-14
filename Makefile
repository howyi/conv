test:
	@vendor/bin/phpunit
stan:
	@vendor/bin/phpstan analyse -l 7 -c phpstan.neon src
seal:
	@vendor/bin/rtrt seal
heat:
	@vendor/bin/rtrt heat

.PHONY: test stan seal heat
