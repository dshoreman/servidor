.PHONY: test

SHELL = bash -eo pipefail
GNU_SED := $(shell command -v gsed || command -v sed)

now := `date '+%Y-%m-%d_%H%M'`
CS_ARGS ?= --show-progress=dots --verbose
INSIGHT_ARGS := --no-interaction $(INSIGHT_ARGS) --verbose
PHP_CSF_ARGS := --diff --dry-run $(CS_ARGS)
PHP_MND_ARGS := --progress $(MND_ARGS) --exclude tests
PHP_STAN_CMD := analyze $(STAN_ARGS)
PHPMD_FORMAT ?= ansi

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
		rm -rf ./{.config/,}composer ./node_modules ./vendor; vagrant destroy -f
	@echo
	@echo "[2/5] Installing Composer/NPM packages and building assets..."
ifeq (, $(shell command -v composer))
	@echo "Composer not available, package installation will run in Vagrant instead."
else
	@composer install --no-interaction --no-progress || true
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

laradiff: laravel-diff

laravel-diff:
	@echo "Removing any old diffs..."
	@rm -rf /tmp/{lara,servi}diff
	@echo "Cloning latest laravel/laravel repo..."
	@git clone --branch 10.x -q git@github.com:laravel/laravel.git /tmp/laradiff
	@echo "Copying configs..."
	@cp -R ./build /tmp/laradiff/
	@echo "Applying CS fixes..."
	@PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix -q --config /tmp/laradiff/build/php-cs-fixer/config.php
	@echo "Updating App namespace..."
	@find /tmp/laradiff -type f -name '*.php' -exec sed -i 's/App\\/Servidor\\/g' {} +
	@echo "Removing comments from (some) config files..."
	@sed -e '/^$$/N;/^\n$$/D' -e '/^\s\+\(\/\*\|\*\/\|\(|\|\*\).*\)/d' -i /tmp/laradiff/config/{app,database,filesystems,mail}.php
	@echo "Updating paths in PHPUnit config..."
	@sed -i 's^\./^../../^g' /tmp/laradiff/phpunit.xml
	@echo "Moving files into place..."
	@mv /tmp/laradiff/app/{Models/,}User.php
	@mv /tmp/laradiff/phpunit.xml /tmp/laradiff/build/phpunit/config.xml
	@echo "Patching files with Servidor changes..."
	@rm -rf /tmp/laradiff/{.git,.styleci.yml,app/Models,resources/{css,views/welcome.blade.php}}
	@cp -R ./{.composer,.git,.github,.idea,node_modules,notes,.vagrant,vendor} /tmp/laradiff/
	@cp ./bootstrap/cache/* /tmp/laradiff/bootstrap/cache/
	@cp -R {.,/tmp/laradiff}/app/Console/Commands
	@cp -R {.,/tmp/laradiff}/app/Http/Requests
	@cp -R ./app/{Databases,FileManager,Projects,Rules,System,Traits,{StatsBar,helpers}.php} /tmp/laradiff/app/
	@cp -R ./app/Http/Controllers/{Auth,Databases,Files,Projects,System,User,{Fallback,SystemInformation}Controller.php} /tmp/laradiff/app/Http/Controllers/
	@cp {.,/tmp/laradiff}/app/Http/Middleware/CheckRegistration.php
	@cp ./config/{clockwork,ide-helper}.php /tmp/laradiff/config/
	@cp ./database/migrations/* /tmp/laradiff/database/migrations/
	@cp {.,/tmp/laradiff}/database/seeders/DefaultUserSeeder.php
	@cp -R ./resources/{sass,test-skel,views} /tmp/laradiff/resources/
	@cp -R ./public/{css,fonts,js,mix-manifest.json} /tmp/laradiff/public/
	@cp -R ./resources/js/{components,layouts,pages,plugins,store,routes.js} /tmp/laradiff/resources/js/
	@cp -R ./storage/framework/{cache,sessions,testing,views} /tmp/laradiff/storage/framework/
	@cp -R ./storage/{app,clockwork,logs} /tmp/laradiff/storage/
	@cp -R ./tests/{Feature,Unit,reports,{Prunes,Requires,Validates}*.php} /tmp/laradiff/tests/
	@cp ./{composer.lock,package-lock.json,_ide_helper.php,*.log,.*.{cache,meta.php}} /tmp/laradiff/
	@cp ./{.env,*.md,LICENSE,Makefile,setup.sh,Vagrantfile} /tmp/laradiff
	@rm /tmp/laradiff/database/migrations/*_create_personal_access_tokens_table.php
	@echo
	@echo "Done! Opening diff tool..."
	@meld /tmp/laradiff .

test: test-8.1 test-8.2

test-8.1:
	@vagrant ssh -c "cd /var/servidor && sudo -u www-data phpdbg8.1 -qrr vendor/bin/phpunit -c build/phpunit/config.xml $(test)"

test-8.2:
	@vagrant ssh -c "cd /var/servidor && sudo -u www-data phpdbg8.2 -qrr vendor/bin/phpunit -c build/phpunit/config.xml $(test)"

test-for-ci:
	vendor/bin/phpunit -c build/phpunit/config.xml --coverage-clover=coverage.xml

coverage:
	@vagrant ssh -c "cd /var/servidor && sudo -u www-data php8.1 vendor/bin/phpunit -c build/phpunit/config.xml --coverage-text"
	@echo

coverage-html:
	vagrant ssh -c "cd /var/servidor && sudo -u www-data php8.1 vendor/bin/phpunit -c build/phpunit/config.xml --coverage-html tests/reports/coverage/latest"
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
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix $(PHP_CSF_ARGS) --config build/php-cs-fixer/config.php

phpcsfix:
	PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix $(CS_ARGS) --config build/php-cs-fixer/config.php

phpcs:
	vendor/bin/phpcs app -p --standard=PSR12

insights:
	vendor/bin/phpinsights $(INSIGHT_ARGS) --config-path=build/phpinsights/config.php

phpmd:
	vendor/bin/phpmd app ${PHPMD_FORMAT} build/phpmd/rules.xml

phpmnd:
	vendor/bin/phpmnd . $(PHP_MND_ARGS)

syntax: eslint phpcsf phpcs phpmd phpmnd phpstan psalm phan insights

kitchen-sink: clear-cscache syntax coverage metrics
