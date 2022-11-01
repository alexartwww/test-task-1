HELP_FUN = \
		%help; \
		while(<>) { push @{$$help{$$2 // 'options'}}, [$$1, $$3] if /^(\w+)\s*:.*\#\#(?:@(\w+))?\s(.*)$$/ }; \
		print "usage: make [target]\n\n"; \
	for (keys %help) { \
		print "$$_:\n"; $$sep = " " x (20 - length $$_->[0]); \
		print "  $$_->[0]$$sep$$_->[1]\n" for @{$$help{$$_}}; \
		print "\n"; }

help:           ##@miscellaneous Show this help.
	@perl -e '$(HELP_FUN)' $(MAKEFILE_LIST)

up: ## Starts the app
	docker-compose up -d
	docker-compose ps

down: ## Stops the app
	docker-compose down

shell:
	docker-compose run php /bin/sh

backup: ## Backups the app
	docker-compose run mysql mysqldump "mysql" -u"root" --password="1234" -h"mysql" --lock-tables=false\
	 | grep -v "Using a password on the command line interface can be insecure" | gzip -9 > backups/$(shell date +%Y-%m-%d-%H-%M-%S).sql.gz

restore: ## Restores the app
	@echo "Restoring from init.sql"
	cat src/init/init.sql | docker-compose run mysql mysql -u"root" --password="1234" -h"mysql"
	@echo "Restoring from "$(shell ls -1r backups/*.sql.gz | head -1)
	cat $(shell ls -1r backups/*.sql.gz | head -1) | gunzip | docker-compose run mysql mysql karma8 -u"root" --password="1234" -h"mysql"

mysql: ## Mysql shell
	docker-compose run mysql mysql karma8 -u"root" --password="1234" -h"mysql" --prompt="\u@\h [\d] > "

alters:
	@echo "Executing alters from src/init/alters.sql"
	cat src/init/alters.sql | docker-compose run mysql mysql -u"root" --password="1234" -h"mysql"

composer:
	docker-compose run php composer install

init: restore alters composer

start: up

stop: down

restart: stop start

build:
	docker build --rm -t karma8 .

check_email:
	docker-compose run php php src/jobs/check_email.php

send_email:
	docker-compose run php php src/jobs/send_email.php
