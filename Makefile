docker-purge-all: docker-purge-containers docker-purge-images

docker-purge-containers:
	docker rm --force $(shell docker ps -qa)

docker-purge-images:
	docker rmi --force $(shell docker images -qa)

docker-build:
	docker build -t titomiguelcosta/gobie-php82 -f docker/gobie-php82.dockerfile .
	docker build -t titomiguelcosta/gobie-php82 -f docker/gobie-php83.dockerfile .

docker-push:
	docker push titomiguelcosta/gobie-php82
	docker push titomiguelcosta/gobie-php83

docker-ssh:
	docker run --entrypoint /bin/bash -it titomiguelcosta/gobie-php83

php-cs-fixer:
	php vendor/bin/php-cs-fixer fix src/

phpunit:
	php vendor/bin/phpunit
