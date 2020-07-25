# task-manager

- Run composer install

- Run php bin/console make:migration
- Run php bin/console doctrine:migrations:migrate

Visit localhost and start adding your tasks.

# For console managing:

- Manage tasks
  Execute on root project: php bin/console task:manager task_name [start or stop]

- List tasks
  Execute on root project: php bin/console task:list