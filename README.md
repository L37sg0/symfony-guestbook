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

### check with curl if HTTP Cache Kernel is active
```bash
curl -s -I -X GET http://localhost
```
```bash
# First request is a 'miss'
HTTP/1.1 200 OK
Server: nginx/1.23.4
Content-Type: text/html; charset=UTF-8
Content-Length: 68418
Connection: keep-alive
X-Powered-By: PHP/8.2.12
Cache-Control: public, s-maxage=3600
Date: Mon, 27 Nov 2023 12:01:37 GMT
X-Debug-Token: cea4e1
X-Debug-Token-Link: http://localhost/_profiler/cea4e1
X-Robots-Tag: noindex
X-Content-Digest: end88bc5ef519a8c5515eb16831d43377c
Age: 0
X-Symfony-Cache: GET /: miss, store

# Second request is a 'fresh' and Age is also changed
HTTP/1.1 200 OK
Server: nginx/1.23.4
Content-Type: text/html; charset=UTF-8
Content-Length: 68418
Connection: keep-alive
X-Powered-By: PHP/8.2.12
Cache-Control: public, s-maxage=3600
date: Mon, 27 Nov 2023 12:01:37 GMT
x-debug-token: cea4e1
x-debug-token-link: http://localhost/_profiler/cea4e1
x-robots-tag: noindex
x-content-digest: end88bc5ef519a8c5515eb16831d43377c
Age: 106
X-Symfony-Cache: GET /: fresh
```

### purge cache using curl as per the new route defined
```bash
curl -s -I -X PURGE -u admin:password http://localhost/admin/http-cache/
curl -s -I -X PURGE -u admin:password http://localhost/admin/http-cache/conference_header
```

### sets API_ENDPOINT env var for the spa
```bash
docker-compose exec -e API_ENDPOINT="http://symfony-guestbook.loc/" app npm run dev --prefix symfony-guestbook/spa/
```

### create cordova app in spa and add android support for it
```bash
docker-compose exec app npm run cordova create app --prefix symfony-guestbook/spa/
docker-compose exec app npm run cordova platform add android --prefix symfony-guestbook/spa/app
# then run npm run dev to build and copy content of public to app/www
rm -rf spa/app/www/
mkdir -p spa/app/www
cp -r spa/public/ spa/app/www/

```

## Notes:
 - workflows should be probably used with some additional checks.
 - DO NOT forget to clean your cached views for the env you're working on when make changes on templates
   ```bash
   rm -rf var/cache/dev
   ```
