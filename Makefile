docker-purge:
	docker rm --force $(docker ps -qa) && docker rmi --force $(docker images -qa)

docker-build:
	docker build -t titomiguelcosta/grooming-chimps-php73 -f docker/grooming-chimps-php73.dockerfile .

docker-push:
	docker push titomiguelcosta/grooming-chimps-php73
