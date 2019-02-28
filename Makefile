#!/usr/bin/make
.SILENT:
.PHONY: help
DC=docker-compose
DCC=$(DC) -f docker-compose.yaml
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

## Setup environment
setup:
	touch .env.local
	touch .env.prod
	[[ -f ./config/nginx/htpasswd.conf ]] || cp Resources/htpasswd.conf config/nginx/htpasswd.conf;
	[[ -f docker-compose.yaml ]] || cp Resources/docker-compose.yaml docker-compose.yaml;
	[[ -f .env ]] || cp .env.dist .env;
	$(DCC) up --no-start;
	printf "${COLOR_COMMENT}Setup Done.${COLOR_RESET}\n"

## Install PHP libs
install:
	composer self-update && composer install --prefer-dist
	./bin/console doctrine:phpcr:register-system-node-types

## Load fixtures
fixtures: install
fixtures:
	./bin/console butler:import:directory Resources/fixture/ --splitter=Resources

## Start the webserver
start:
	$(DCC) up -d content-server nginx;
	printf "${COLOR_COMMENT}Web server started.${COLOR_RESET}\n"

## Stop the webserver
stop:
	$(DCC) down;
	printf "${COLOR_COMMENT}Web server stoped.${COLOR_RESET}\n"

## Restart the webserver
restart: stop start

## Setup, install and run with fixtures
demo: setup start
demo:
	$(DC) run --rm php-fpm make fixtures


## Backup current files
backup:
	docker cp "$(docker-compose ps -q content-server)":/opt/jackrabbit ./var/opt/
	printf "${COLOR_COMMENT}Backup done.${COLOR_RESET}\n"
