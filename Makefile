start-daemon:
	docker-compose -f build/container/dev/docker-compose.yml up -d
phpunit:
	docker-compose -f build/container/dev/docker-compose.yml exec php-fpm bin/phpunit $(TEST)

encore-watch:
	docker-compose -f build/container/dev/docker-compose.yml exec php-fpm yarn encore dev --watch

encore-dev-server:
	docker-compose -f build/container/dev/docker-compose.yml exec php-fpm yarn encore dev-server --hot
