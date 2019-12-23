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


## Load fixtures
fixtures: install
fixtures:
	./bin/console butler:import:directory Resources/fixture/ --splitter=Resources

## Register node types
register:
	./bin/console doctrine:phpcr:register-system-node-types
	printf "${COLOR_COMMENT}System nodes types OK.${COLOR_RESET}\n"

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
demo: setup start register
demo:
	$(TOOLSDC) run --rm php-fpm make fixtures
