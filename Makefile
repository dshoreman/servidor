.PHONY: test

now := `date '+%Y-%m-%d_%H%M'`

test:
	@vagrant ssh -c "cd /var/servidor && sudo -u www-data php vendor/bin/phpunit -c build/phpunit/config.xml"

coverage:
	@vagrant ssh -c "cd /var/servidor && sudo -u www-data php vendor/bin/phpunit -c build/phpunit/config.xml --coverage-text"
	@echo

coverage-html:
	vagrant ssh -c "cd /var/servidor && sudo -u www-data phpdbg -qrr vendor/bin/phpunit --coverage-html tests/reports/coverage/latest"
	@mv tests/reports/coverage/latest tests/reports/coverage/$(now)/ && xdg-open ./tests/reports/coverage/$(now)/index.html
	@echo

metrics:
	vendor/bin/phpmetrics app --git

metrics-html:
	vendor/bin/phpmetrics app --report-html="./tests/reports/metrics/$(now)" --git

reports: coverage-html metrics-html

phpstan:
	vendor/bin/phpstan analyze -c build/phpstan/config.neon

phpcsf:
	vendor/bin/php-cs-fixer fix --diff --dry-run --config build/php-cs-fixer/config.php
	@echo

phpcs:
	vendor/bin/phpcs app -p --standard=PSR12

phpmd:
	vendor/bin/phpmd app ansi build/phpmd/rules.xml
	@echo

phpmnd:
	vendor/bin/phpmnd . --progress --exclude tests
	@echo

syntax: phpcsf phpcs phpmd phpmnd

kitchen-sink: syntax phpstan coverage metrics
