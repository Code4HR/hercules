Hercules: Hampton Roads Quality of Life Service
===============================================
Want to help? We track [this project in Waffle.io](https://waffle.io/Code4HR/hercules) [![Stories in Ready](https://badge.waffle.io/Code4HR/hercules.png?label=ready&title=Ready)](https://waffle.io/Code4HR/hercules)

# About
HRQLS is a data analytics project to help citizens get facts and make decisions by layering various data points on maps. It's an expirement on pulling together various demographics, city data sets, and other local data layers to see if new insights can be gained from seeing it mashed together.

The project was the Best In Show winner at the fall 2015 Hack to Help Hampton Roads hackathon, organized by Dominion Enterprises & Code for Hampton Roads. DE donated $15,000 to Code for America to help usher this project into a useful production system.

# Installation Locally for Development

1. Make sure you have [Docker and Docker Compose installed](https://www.docker.com/) and have at least run through their tutorial.
1. Clone this repo.
1. If you have PHP 5.x and Composer installed locally on your machine, run `composer install` once in the repo directory to pull dependencies, OR
1. Use a Composer Docker container to do it for you: `docker run --rm -v $(pwd):/app composer/composer:php5 install` will download a Composer image, mount your repo, download dependencies, and then remove itself.
1. `Use our docker-compose-dev-example.yml` as a starting point. If you copy it to `docker-compose.yml` and change something like the port to your preference, you can bring up the site so you can start editing in your repo with just `docker-compose up -d`.
1. The defaults with the compose file are to use our prod elasticsearch instance and use our pre-built image rather then building your local repo. If you need to change things like composer add-on's or custom Apache commands, you'll need to use a `build: .` line in docker-compose rather then the `image: code4hr/hercules:docker` line

# Team

- [@BretFisher](https://github.com/bretfisher) - PM and CfA Sponsor
- [@DerekDrummond](https://github.com/ezzy1337) - Professional code typer
- [@KrishnaRanga](https://github.com/krishnaramya) - Master of AJAX
- [@JosiahBaker](https://github.com/josibake) - Data Dude
- [@JasonBennett](https://github.com/blackhatbrigade) - Master of Automation
- [@AliciaSedarski](https://github.com/asedarski) - Makes it look pretty

## Other Information

The autoloader will resolve any references to "HRQLS" to src/HRQLS.

