# Content Butler

Content server with Apache Jackrabbit (backend) and Nginx proxy (frontend)

[![Paypal Donations](https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WAQKVZJRG5AUJ&item_name=content-butler)

[![Build Status](https://secure.travis-ci.org/gpupo/content-butler.png?branch=master)](http://travis-ci.org/gpupo/content-butler)

## Requirements

* PHP >= *7.2*
* [Composer Dependency Manager](http://getcomposer.org)
* Docker compose

## Features

* Content Repository with Apache Jackrabbit
* Webpage server with Image resize

## Install

    git clone git@github.com:gpupo/content-butler.git;
    cd content-butler;

Copy config files (and customize)

    cp .env.dist .env; #optional
    cp Resources/docker-compose.prod.yml docker-compose.yml
    cp config/nginx/htpasswd.dist.conf config/nginx/htpasswd.conf


Set passwords: default user is admin with admin password. You must edit htpasswd file with new values and that [generator](http://www.htaccesstools.com/htpasswd-generator/) is a usefull tool.

Create docker volume and machines

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

## Library usage

### Install

	composer require gpupo/content-butler

### Add Documents

Versionable, Overshadow and Millenial Tree:

```php
//...
use Gpupo\ContentButler\Helpers\DocumentHelper;
$documentHelper = new DocumentHelper($this->documentManager, $spliter = 'Resources', $versionable = true);
$document = $documentHelper->factoryDocument('var/file/path.jpg', 8068, true);

if ($this->documentManager->find(null, $document->getEndpoint())) {
    throw new \Exception(sprintf('Node %s already exists', $document->getEndpoint()));
}

$output->writeln(sprintf('Saving node <info>%s</>', $document->getEndpoint()));
$this->documentManager->persist($document);
$this->documentManager->flush();
```

## Links


See
* [Jackalope Jackrabbit by example](https://github.com/gpupo/jackalope-jackrabbit-by-example)
* [content-repository-server](https://github.com/gpupo/content-repository-server)
* [content-butler](https://github.com/gpupo/content-butler)
