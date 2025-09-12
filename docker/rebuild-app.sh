#!/bin/bash

if [ -f "./.env" ]
then
    export $(cat ./.env | sed 's/#.*//g' | xargs)
else
    echo -e "\033[0;31mPlease duplicate .env.example as .env as change variables accordingly\033[0m"
    exit 1;
fi

if [ -z ${PROJECT_NAME+x} ];
then
    PROJECT_NAME="sample"
fi

if docker compose version
then
  docker compose -f ./docker-compose.yml -p "${PROJECT_NAME}" up -d --build app || exit 1;
elif docker-compose --version
then
  docker-compose -f ./docker-compose.yml -p "${PROJECT_NAME}" up -d --build app || exit 1;
fi
