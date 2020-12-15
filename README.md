# Broker

Service which creates messages and send them to the Kafka topics  

## Requirements
 - [Docker](https://docs.docker.com/get-docker/)
 - [Docker compose](https://docs.docker.com/compose/)
 - [Docker machine](https://docs.docker.com/machine/)
 - [Virtualbox](https://www.virtualbox.org/)
 - [Composer](https://getcomposer.org/)

## Before

The dependencies of the project need to be satisfied, for that
if you have composer installed globally run:

```
$ composer install 
```
 
If not follow the steps in the [docs](https://getcomposer.org/download/) and then run

```
$ php composer.phar install 
```

On the root of the project

### Configuring the project

Copy the contents of the .env.example file

```
$ cp src/.env.example src/.env
```

Adjust the values as needed

For kafka the url can be directed to localhost on the 9091 port 
if you have the service consumer running(which contains a Kafka service).

```
KAFKA_URL=kafka
KAFKA_PORT=9091
```

The database is 
 
### Starting the project

To initialize the virtual machine and set the environment you can run 

```
$ source setenv
``` 

from the root of the project.

After the machine gets created you can run the application with:

```
$ docker-compose up
```

or if you prefer simply run docker compose
```
$ docker-compose up -d
```  

## Initialize the db:

Go into the php container:
```
$ docker exec -it broker_php_1 bash
```
and execute the next command from within the service:
```
$ vendor/bin/doctrine orm:schema-tool:create
```

This should create the needed db schemas

# Notes:
The guest exposes the next IP:
```
192.168.100.100
```
 So the application is accessible through this IP from the host machine
 and this value can be used to configure the `consumer` and `requester` applications
