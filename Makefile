docker-purge-all: docker-purge-containers docker-purge-images

docker-purge-containers:
	docker rm --force $(shell docker ps -qa)

docker-purge-images:
	docker rmi --force $(shell docker images -qa)

docker-build:
	docker build -t titomiguelcosta/gobie-php80 -f docker/gobie-php80.dockerfile .
	docker build -t titomiguelcosta/gobie-php81 -f docker/gobie-php81.dockerfile .

docker-push:
	docker push titomiguelcosta/gobie-php80
	docker push titomiguelcosta/gobie-php81

docker-ssh:
	docker run --entrypoint /bin/bash -it titomiguelcosta/gobie-php81

php-cs-fixer:
	php vendor/bin/php-cs-fixer fix src/

phpunit:
	php vendor/bin/phpunit
