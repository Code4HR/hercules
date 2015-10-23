#!/bin/bash

docker run -i -t --rm \
    --name hackathon \
    -p 80:80 \
    -p 9200:9200 \
    -v ${HOME}/hackathon/HRQLS:/var/www/html \
    hackathon/hrqls
