# Installation

1) Make sure you have docker installed and functional on the system this will be used on.  ( https://docs.docker.com/ )

2) Clone this repo into the desired location and run `docker build -t hackathon/hrqls .`

3) Run the startup script `runContainer.sh` to start the docker container.

4) Make sure to run `composer install` in the base directory of the project to pull all dependencies. ( https://getcomposer.org/download/ )


## Other Information

The autoloader will resolve any references to "HRQLS" to src/HRQLS.
