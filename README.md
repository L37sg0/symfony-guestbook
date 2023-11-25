# Symfony Guestbook

## Tutorial application following symfony 6 book

https://symfony.com/doc/6.2/the-fast-track/en

## Differences:
 - is using mysql instead of postgresql
 - symfony CLI is not installed
 - code is not deployed on platform.sh

## Used Commands in the tutorial - only the rare ones

### asks you for a plain password and returns to you a hash
```bash
docker-compose exec app symfony-guestbook/bin/console security:hash-password
```

### makes a testcase of the specified type
```bash
docker-compose exec app symfony-guestbook/bin/console make:test TestCase SpamCheckerTest
```

### creates the database specified in the pointed config
```bash
docker-compose exec app symfony-guestbook/bin/console doctrine:database:create --env=test
```

### loads the fixtures(fake date) in the database (--env=test will be for the test database)
```bash
docker-compose exec app symfony-guestbook/bin/console doctrine:fixtures:load --env=test
```

### consumes the messages submitted to the message bus
```bash
docker-compose exec app symfony-guestbook/bin/console messenger:consume async -vv
```

### install the workflow bundle
```bash
docker-compose exec app composer -d symfony-guestbook/ require symfony/workflow
```

### generate the image with the workflow
```bash
docker-compose exec app symfony-guestbook/bin/console workflow:dump comment | dot -Tpng -o ./symfony-guestbook/workflow.png
```

### explicitly find workflow services from the dependency injection container
```bash
docker-compose exec app symfony-guestbook/bin/console debug:container workflow
```
```bash
docker-compose exec app symfony-guestbook/bin/console debug:autowiring workflow
```

## Notes:
 - workflows should be probably used with some additional checks.