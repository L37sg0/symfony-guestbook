SHELL := /bin/bash

tests:
	docker-compose exec app symfony-guestbook/bin/console doctrine:database:drop --force --env=test || true
	docker-compose exec app symfony-guestbook/bin/console doctrine:database:create --env=test
	docker-compose exec app symfony-guestbook/bin/console doctrine:migrations:migrate -n --env=test
	docker-compose exec app symfony-guestbook/bin/console doctrine:fixtures:load -n --env=test
	docker-compose exec app symfony-guestbook/bin/phpunit $(MAKECMGOALS)
.PHONY: tests