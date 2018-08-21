# Content Butler

Content server with Apache Jackrabbit (backend) and Nginx proxy (frontend)

## Requirements

* Docker Compose

## Features

* Content Repository with Apache Jackrabbit
* Webpage server with Image resize

## Install

    git clone git@github.com:gpupo/content-butler.git;
    cd content-butler;

Copy config files (and customize)

    cp .env.dist .env; #optional
    cp docker-compose.dist.yml docker-compose.yml
    cp config/nginx/htpasswd.dist.conf config/nginx/htpasswd.conf


Set passwords: default user is admin with admin password. You must edit htpasswd file with new values and that [generator](http://www.htaccesstools.com/htpasswd-generator/) is a usefull tool.


Create docker volume and machines

    docker volume create jackrabbit-storage; #optional
    docker-compose up --no-start;

Install dependencies

    docker-compose run php composer install

Copy files to machines (optional)

    docker cp config/empty.gif "$(docker-compose ps -q nginx)":/usr/share/nginx/html/empty.gif;

If previous repository exists (see backup):

    docker cp var/opt/jackrabbit/repository "$(docker-compose ps -q content-server)":/opt/jackrabbit/;
    docker cp var/opt/jackrabbit/workspaces "$(docker-compose ps -q content-server)":/opt/jackrabbit/;
    docker cp var/opt/jackrabbit/version "$(docker-compose ps -q content-server)":/opt/jackrabbit/;

Up docker services

    docker-compose up -d;

Register node types

    docker-compose run php bin/console doctrine:phpcr:register-system-node-types;

### Alternative storage location

    docker cp  "$(docker-compose ps -q content-server)":/opt/jackrabbit var/jackrabbit;

add volume to docker file:

    - $PWD/var/jackrabbit:/opt/jackrabbit


## Backup

### Export

Export repository with Filesystem Copy:

    docker cp "$(docker-compose ps -q content-server)":/opt/jackrabbit var/opt/;

Clone (SVN style)

    docker-compose run java bin/clone var/dest_directory;

### Import

Example, load fixture:

    docker-compose run php ./bin/console butler:import:directory Resources/fixture/ --splitter=Resources;

or

	docker-compose run php bin/fixture

Load SVN style

    docker-compose run php bin/console butler:import:directory var/clone;

## Explore

Content Repository

1. First, import fixtures;
1. Check [Jackrabbit dashboard](http://localhost:8080/)
1. [Browse files](http://localhost:8080/repository/default/) and see some [Sheeps](http://localhost:8080/repository/default/fixture/extra/photos/sheep-3562868-pixabay.jpg)

Nginx frontend

1. Check if you keep seen [sheeps](http://localhost/fixture/extra/photos/sheep-3562868-pixabay.jpg)
1. Check if you can seen [litle sheeps](http://localhost/img/100x100/fixture/extra/photos/sheep-3562868-pixabay.jpg)
1. Browse [default repository](http://localhost/repository/default)
