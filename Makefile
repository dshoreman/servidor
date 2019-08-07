.PHONY: test

test:
	@vagrant ssh -c "cd /var/servidor && sudo -u www-data php vendor/bin/phpunit"
