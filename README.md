# Content Butler

Content server with Apache Jackrabbit (backend) and Nginx proxy (frontend)

## Requirements

* Docker
* Composer


## Install

    git clone git@github.com:gpupo/content-butler.git
    cd content-butler;
    composer install;

Create docker machines

    docker-compose up --no-start

Copy files to machines (optional)

    docker cp config/empty.gif "$(docker-compose ps -q nginx)":/usr/share/nginx/html/empty.gif

    #If previous repository exists:
    docker cp var/opt/jackrabbit/repository "$(docker-compose ps -q content-server)":/opt/jackrabbit/
    docker cp var/opt/jackrabbit/workspaces "$(docker-compose ps -q content-server)":/opt/jackrabbit/
    docker cp var/opt/jackrabbit/version "$(docker-compose ps -q content-server)":/opt/jackrabbit/


Up docker services

    docker-compose up

Register node types

    ./bin/console doctrine:phpcr:register-system-node-types

## Backup

### Export

Export repository with Filesystem Copy:

    docker cp "$(docker-compose ps -q content-server)":/opt/jackrabbit var/opt/

Clone (SVN style)

    ./bin/clone var/clone


### Import

Example, load fixture:

	./bin/console butler:import:directory Resources/fixture/ --splitter=Resources

Load SVN style

	./bin/console butler:import:directory var/clone
