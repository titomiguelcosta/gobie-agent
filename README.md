# Agent for Grooming Chimps 

The agent is responsible for executing a job.

It runs on AWS Batch. And reports back to the API.

## Executing

The agent runs in pre-compiled docker images on AWS Batch.
At the moment, only one docker image is available.
Configuration is the docker directory and there are make commands to build it.

### Images

* titomiguelcosta/grooming-chimps-php73: PHP 7.3 with several PHP packages pre-installed

### Environment variables

GROOMING_CHIMPS_API_AUTH_TOKEN - Authentication token of the user that requested to process the job
GROOMING_CHIMPS_API_USER_USERNAME - The username of the user that started the job (optional)
GROOMING_CHIMPS_API_BASE_URI - The URL of the API it reports the job results 
GROOMING_CHIMPS_API_JOB_ID - ID of the job to process

