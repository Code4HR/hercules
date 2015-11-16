Hercules: Hampton Roads Quality of Life Service
===============================================
Want to help? We track [this project in Waffle.io](https://waffle.io/Code4HR/HRQLS) [![Stories in Ready](https://badge.waffle.io/Code4HR/HRQLS.png?label=ready&title=Ready)](https://waffle.io/Code4HR/HRQLS)

# About
HRQLS is a data analytics project to help citizens get facts and make decisions by layering various data points on maps. It's an expirement on pulling together various demographics, city data sets, and other local data layers to see if new insights can be gained from seeing it mashed together.

The project was the Best In Show winner at the fall 2015 Hack to Help Hampton Roads hackathon, organized by Dominion Enterprises & Code for Hampton Roads. DE donated $15,000 to Code for America to help usher this project into a useful production system.

# Installation

1) Make sure you have docker installed and functional on the system this will be used on.  ( https://docs.docker.com/ )

2) Clone this repo into the desired location and run `docker build -t hackathon/hrqls .`

3) Run the startup script `runContainer.sh` to start the docker container.

4) Make sure to run `composer install` in the base directory of the project to pull all dependencies. ( https://getcomposer.org/download/ )

# Team
TODO: Someone add our team member github usernames and their basic roles
- @BretFisher - PM and CfA Sponsor

## Other Information

The autoloader will resolve any references to "HRQLS" to src/HRQLS.
