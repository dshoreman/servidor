.PHONY: test

SHELL = bash -eo pipefail
GNU_SED := $(shell command -v gsed || command -v sed)

now := `date '+%Y-%m-%d_%H%M'`
PHP_CSF_ARGS := --diff --dry-run $(CS_ARGS)
PHP_MND_ARGS := --progress $(MND_ARGS) --exclude tests
PHP_STAN_CMD := analyze $(STAN_ARGS)

installer: thinkdifferent
	@echo -n "Building unified install script... "
	@cat build/installer/main.sh build/installer/_*.sh | \
		$(GNU_SED) -e '1,30s/echo "\(\s\+\[\)/echo "        \1/' \
			-e '1,20{/^# shellcheck source=_.*$$/,+1d}' \
			-e '/^main "$$@"$$/{H;d};$${p;x;s/^\n//}' \
			-e 's^main\.sh^bash ./setup.sh^' \
			-e '/^\(SCRIPT_ROOT=\|$$\)/d' \
		> setup.sh
	@chmod +x setup.sh && echo "Done!"

dev-env:
	@echo "Go stick the kettle on, this'll take a while." && sleep 2
	@echo
	@echo "[1/5] Destroying Vagrant VM and purging package caches..." && \
		rm -rf ./composer ./node_modules ./vendor; vagrant destroy -f
	@echo
	@echo "[2/5] Installing Composer/NPM packages and building assets..."
ifeq (, $(shell command -v composer))
	@echo "Composer not available, package installation will run in Vagrant instead."
else
	@composer install --no-interaction --no-progress --no-suggest || true
endif
	@npm ci && npm run dev
	@echo
	@echo "[3/5] Creating the new VM..." && \
		vagrant up --no-provision || true
	@echo
	@echo "[4/5] Creating the 'servidor' user..." && \
		vagrant provision --provision-with=user
	@echo
	@echo "[5/5] Restarting Vagrant VM to run installer..." && \
		make installer && vagrant reload --provision-with=installer

thinkdifferent:
ifeq (Darwin,$(shell uname)$(shell command -v gsed))
	@echo "To build the installer on OSX, you must first brew install gnu-sed"
	@exit 1
endif

test:
	@vagrant ssh -c "cd /var/servidor && sudo -u www-data phpdbg -qrr vendor/bin/phpunit -c build/phpunit/config.xml"

test-for-ci:
	vendor/bin/phpunit -c build/phpunit/config.xml --coverage-clover=coverage.xml --exclude-group "broken-travis"

coverage:
	@vagrant ssh -c "cd /var/servidor && sudo -u www-data php vendor/bin/phpunit -c build/phpunit/config.xml --coverage-text"
	@echo

coverage-html:
	vagrant ssh -c "cd /var/servidor && sudo -u www-data php vendor/bin/phpunit -c build/phpunit/config.xml --coverage-html tests/reports/coverage/latest"
	@mv tests/reports/coverage/latest tests/reports/coverage/$(now)/ && xdg-open ./tests/reports/coverage/$(now)/index.html
	@echo

metrics:
	vendor/bin/phpmetrics app --git

metrics-html:
	vendor/bin/phpmetrics app --report-html="./tests/reports/metrics/$(now)" --git

reports: coverage-html metrics-html

clear-cscache:
	vendor/bin/psalm -c build/psalm/psalm.xml --clear-cache

eslint:
	node_modules/.bin/eslint -c build/eslint/config.json resources/js --ext .js,.vue
	@echo -e "\n\e[1;32m✔ 0 problems (0 errors, 0 warnings)\e[0m"

phan:
	vendor/bin/phan --config-file build/phan/config.php --color

phpstan:
	php -d memory_limit=-1 vendor/bin/phpstan $(PHP_STAN_CMD) -c build/phpstan/config.neon

psalm:
	vendor/bin/psalm -c build/psalm/psalm.xml

phpcsf:
	vendor/bin/php-cs-fixer fix $(PHP_CSF_ARGS) --config build/php-cs-fixer/config.php

phpcs:
	vendor/bin/phpcs app -p --standard=PSR12

phpmd:
	vendor/bin/phpmd app ansi build/phpmd/rules.xml

phpmnd:
	vendor/bin/phpmnd . $(PHP_MND_ARGS)

syntax: eslint phpcsf phpcs phpmd phpmnd phpstan psalm phan

kitchen-sink: clear-cscache syntax coverage metrics
