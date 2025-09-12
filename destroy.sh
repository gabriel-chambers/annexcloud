#!/bin/bash

if [ -f .env ]
then
    export $(cat .env | sed 's/#.*//g' | xargs)
fi

if [ -z ${PROJECT_NAME+x} ]; 
then 
    PROJECT_NAME="sample"
fi

#Removing the app container
echo -e "\033[0;34mRemoving the containers...\033[0m"
docker-compose -f ./docker/docker-compose.yml -p "${PROJECT_NAME}" down || exit 1;

#Removing the proxy server container
echo -e "\033[0;34mRemoving the proxy server\033[0m"
docker-compose -f ./docker/docker-compose-proxy.yml down || exit 1;
