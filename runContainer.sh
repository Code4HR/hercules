#!/bin/bash

docker run -i -t --rm \
    --name hackathon \
    -p 80:80 \
    -v .:/www/files \
    hackathon/HRQLS
