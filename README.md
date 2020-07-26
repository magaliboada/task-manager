### Containers
 - [nginx](https://pkgs.alpinelinux.org/packages?name=nginx&branch=v3.10) 1.18.+
 - [php-fpm](https://pkgs.alpinelinux.org/packages?name=php7&branch=v3.10) 7.4.+
    - [composer](https://getcomposer.org/) 
    - [yarn](https://yarnpkg.com/lang/en/) and [node.js](https://nodejs.org/en/) (if you will use [Encore](https://symfony.com/doc/current/frontend/encore/installation.html) for managing JS and CSS)
- [mysql](https://hub.docker.com/_/mysql/) 5.7.+

### Installing

run docker:
 docker-compose up --build
 docker-compose exec php sh
 
 # task-manager

- Run composer install

- Run php bin/console make:migration
- Run php bin/console doctrine:migrations:migrate

Visit localhost and start adding your tasks.

# For console managing:

- Manage tasks
  Execute on root symfony project: php bin/console task:manager task_name [start or end]

- List tasks
  Execute on root symfony project: php bin/console task:list


 
