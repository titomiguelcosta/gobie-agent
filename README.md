# Agent for Gobie

The agent is responsible for executing a job. A job consists of multiple tasks. A task is not more than a script that runs on the command line.

It runs on AWS Batch and reports back to the API.

## Executing

The agent runs in pre-compiled docker images on AWS Batch.

At the moment, there are versions for PHP 7.3, 7.4 and 8.0.

Configuration is in the docker directory and there are make commands to build it.

### Images

* [titomiguelcosta/gobie-php73](https://hub.docker.com/r/titomiguelcosta/gobie-php73): PHP 7.3
* [titomiguelcosta/gobie-php74](https://hub.docker.com/r/titomiguelcosta/gobie-php74): PHP 7.4
* [titomiguelcosta/gobie-php80](https://hub.docker.com/r/titomiguelcosta/gobie-php80): PHP 8.0

### Environment variables

GOBIE_API_AUTH_TOKEN - Authentication token of the user that requested to process the job
GOBIE_API_USER_USERNAME - The username of the user that started the job (optional)
GOBIE_API_BASE_URI - The URL of the API it reports the job results 
GOBIE_API_JOB_ID - ID of the job to process
