#!/usr/bin/make
.SILENT:
.PHONY: help
DC=docker-compose
STANDARTDC=$(DC) -f docker-compose.yaml
TOOLSDC=$(STANDARTDC) -f Resources/docker-compose-tools.yaml
## Colors
COLOR_RESET   = \033[0m
COLOR_INFO  = \033[32m
COLOR_COMMENT = \033[33m
SHELL := /bin/bash

## List Targets and Descriptions
help:
	printf "${COLOR_COMMENT}Usage:${COLOR_RESET}\n"
	printf " make [target]\n\n"
	printf "${COLOR_COMMENT}Available targets:${COLOR_RESET}\n"
	awk '/^[a-zA-Z\-\_0-9\.@]+:/ { \
	helpMessage = match(lastLine, /^## (.*)/); \
	if (helpMessage) { \
	helpCommand = substr($$1, 0, index($$1, ":")); \
	helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
	printf " ${COLOR_INFO}%-16s${COLOR_RESET} %s\n", helpCommand, helpMessage; \
	} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST)

__header:
	if [ ! -f /.dockerenv ]; then \
		printf "\n\n!!! ${COLOR_ERROR}This target is only available for execution inside a container!${COLOR_RESET}\n\n\n"; \
		$(MAKE) help; \
		exit 1; \
	else \
		printf "\n";\
	fi;

__bottom:
	printf "\nTarget ${COLOR_COMMENT}Done!${COLOR_RESET}\n";

## Print system info
info:
	$(TOOLSDC) config
	$(TOOLSDC) ps

## Setup environment
setup:
	[[ -f .env.local ]] || echo '#==== local config ====' > .env.local
	[[ -f .env.prod ]] || echo '#==== prod config ====' > .env.prod
	[[ -f ./config/nginx/htpasswd.conf ]] || cp Resources/htpasswd.conf config/nginx/htpasswd.conf
	[[ -f docker-compose.yaml ]] || cp Resources/docker-compose.yaml docker-compose.yaml
	$(MAKE) build
	$(STANDARTDC) up --no-start
	printf "${COLOR_COMMENT}Setup Done.${COLOR_RESET}\n"

## Build dotenv
build:
	cat `ls -1 ./.env.* | grep -v test` > ./.env
	printf "\nDotenv ${COLOR_COMMENT}Done!${COLOR_RESET}\n"

## Install PHP libs
install:
	$(MAKE) __header
	composer self-update && composer install --prefer-dist
	./bin/console doctrine:phpcr:register-system-node-types
	$(MAKE) __bottom

## Load fixtures
fixtures: install
fixtures:
	./bin/console butler:import:directory Resources/fixture/ --splitter=Resources

## Start the webserver
start:
	$(STANDARTDC) up -d content-server nginx
	printf "${COLOR_COMMENT}Web server started.${COLOR_RESET}\n"

## Stop the webserver
stop:
	$(TOOLSDC) down;
	printf "${COLOR_COMMENT}Web server stoped.${COLOR_RESET}\n"

## Restart the webserver
restart: stop start

## Setup, install and run with fixtures
demo: setup start
demo:
	$(TOOLSDC) run --rm php-fpm make fixtures


## Backup current files
backup@opt:
	docker cp "$(docker-compose ps -q content-server)":/opt/jackrabbit ./var/opt/
	printf "${COLOR_COMMENT}Backup done.${COLOR_RESET}\n"

## Backup current files
backup@svn-style:
	$(TOOLSDC) run --rm java bin/clone var/dest_directory;
	printf "${COLOR_COMMENT}Backup done.${COLOR_RESET}\n"
