docker-purge-all: docker-purge-containers docker-purge-images

docker-purge-containers:
	docker rm --force $(shell docker ps -qa)

docker-purge-images:
	docker rmi --force $(shell docker images -qa)

docker-build:
	docker build -t titomiguelcosta/grooming-chimps-php73 -f docker/grooming-chimps-php73.dockerfile .
	docker build -t titomiguelcosta/grooming-chimps-php74 -f docker/grooming-chimps-php74.dockerfile .
	docker build -t titomiguelcosta/grooming-chimps-php80 -f docker/grooming-chimps-php80.dockerfile .

docker-push:
	docker push titomiguelcosta/grooming-chimps-php73
	docker push titomiguelcosta/grooming-chimps-php74
	docker push titomiguelcosta/grooming-chimps-php80

php-fix:
	php vendor/bin/php-cs-fixer fix src/
