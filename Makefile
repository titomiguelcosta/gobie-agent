docker-purge-all: docker-purge-containers docker-purge-images

docker-purge-containers:
	docker rm --force $(shell docker ps -qa)

docker-purge-images:
	docker rmi --force $(shell docker images -qa)

docker-build:
	docker build -t titomiguelcosta/grooming-chimps-php73 -f docker/grooming-chimps-php73.dockerfile .

docker-push:
	docker push titomiguelcosta/grooming-chimps-php73
