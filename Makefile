.PHONY: test

now := `date '+%Y-%m-%d_%H%M'`

test:
	@vagrant ssh -c "cd /var/servidor && sudo -u www-data php vendor/bin/phpunit"

coverage:
	@vagrant ssh -c "cd /var/servidor && sudo -u www-data php vendor/bin/phpunit --coverage-text"
	@echo

coverage-html:
	vagrant ssh -c "cd /var/servidor && sudo -u www-data php vendor/bin/phpunit --coverage-html tests/reports/coverage/latest"
	@mv tests/reports/coverage/latest tests/reports/coverage/$(now)/ && xdg-open ./tests/reports/coverage/$(now)/index.html
	@echo

metrics:
	vendor/bin/phpmetrics app --git

metrics-html:
	vendor/bin/phpmetrics app --report-html="./tests/reports/metrics/$(now)" --git

reports: coverage-html metrics-html

phpstan:
	vendor/bin/phpstan analyze

psalm:
	vendor/bin/psalm

phpcsf:
	vendor/bin/php-cs-fixer fix --diff --dry-run --using-cache=no
	@echo

phpcs:
	vendor/bin/phpcs app -p --standard=PSR12

phpmd:
	vendor/bin/phpmd app text .phpmd.xml
	@echo

phpmnd:
	vendor/bin/phpmnd . --progress --exclude tests
	@echo

syntax: phpcsf phpcs phpmd phpmnd

kitchen-sink: syntax phpstan psalm coverage metrics
