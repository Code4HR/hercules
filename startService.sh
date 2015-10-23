#!/bin/bash

service apache2 restart
mkdir ./logs
/usr/local/share/elasticsearch/bin/elasticsearch > ./logs/ElasticSearch.log &
