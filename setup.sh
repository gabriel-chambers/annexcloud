#!/bin/bash
echo -e "\033[0;32m\n\n---------- Legolas Docker setup -----\033[0m \n\n"

read -s -n 1 -r -p $'\033[0;33mThis project runs on PORT 80. Please make sure the port is avaialble unless it\'s occupied by "jwilder/nginx-proxy" docker container(Stop the existing apache or nginx servers).\nPress \"Y\" key when you are ready to continue with the setup or any other key to terminate.\n\033[0m' key

if [ "$key" != "Y" ] && [ "$key" != "y" ];then
  exit 1;
fi

echo -e "\033[0;32mPlease wait...\033[0m \n"
if lsof &> /dev/null; then
  echo -e "\033[0;32mTesting port 80...\033[0m \n"
  port_80=$(sudo lsof -Pi :80 -sTCP:LISTEN);
  port_80_docker=$(sudo lsof -Pi :80 -sTCP:LISTEN | grep -E 'com.docke|docker-pr')
  if [[ ! -z $port_80 ]] && [[ -z $port_80_docker ]]; then
    echo -e "\033[0;31mError! Port 80 already occupied by non-docker process.\033[0m"
    echo -e "\033[0;31m{$port_80}\033[0m"
    exit 1;
  else
    echo -e "\033[0;32mPort 80 is avaialble 🙌\033[0m \n"
  fi
fi

if [ -f "./docker/.env" ]
then
    export $(cat ./docker/.env | sed 's/#.*//g' | xargs)
else
    echo -e "\033[0;31mPlease duplicate docker/.env.example as docker/.env as change variables accordingly\033[0m"
    exit 1;
fi

PROJECT_ROOT="${PWD}"
PROJECT_PATH="${PWD}/wp-content"

if [ -z ${PROJECT_NAME+x} ];
then
    PROJECT_NAME="sample"
fi

#Copying wp-config
echo -e "\033[0;34mCreating wp-config file\033[0m"
cp ./docker/wp-config-sample.php ./wp-config.php

#Installing the proxy server
echo -e "\033[0;34mSetting up the proxy server\033[0m"
docker network create proxy
if docker compose version
then
  docker compose -f ./docker/docker-compose-proxy.yml up -d || exit 1;
elif docker-compose --version
then
  docker-compose -f ./docker/docker-compose-proxy.yml up -d || exit 1;
fi

#Adding the host record to etc/hosts
echo -e "\033[0;34mAdding the host record to etc/hosts...\033[0m"
grep ${VIRTUAL_HOST} /etc/hosts || echo "127.0.0.1 ::1 ${VIRTUAL_HOST}" | sudo tee -a /etc/hosts

#Composing the containers
echo -e "\033[0;34mSetting up the containers...\033[0m"
cd ${PWD}
if docker compose version
then
  docker compose -f ./docker/docker-compose.yml -p "${PROJECT_NAME}" up -d || exit 1;
elif docker-compose --version
then
  docker-compose -f ./docker/docker-compose.yml -p "${PROJECT_NAME}" up -d || exit 1;
fi

if  [ ! -d "${PROJECT_PATH}" ]; then
    # Creating a new project using legolas native repository
    echo -e "\033[0;34mCreating a new project from Legolas navite repository...\033[0m"
    cd ${PWD}
    composer -n create-project e25/legolas-native-wp tmp "${LEGOLAS_VERSION}" --repository='{"packagist.org": false}' --repository="https://repo.packagist.com/gandalf/" || exit 1;
    mv tmp/README.md ./tmp/README.wp.md
    mv tmp/* ./
    rm -rf tmp
else
    echo -e "\033[0;34mProject source already available. Installing composer...\033[0m"
    composer install
fi

# Setting up realm if available
if [ -d "${PROJECT_PATH}/plugins/realm" ]; then
    echo -e "\033[0;34mLinking composer.json for Realm...\033[0m"
    sudo chmod 777 -R "${PROJECT_PATH}/plugins/realm"
    cd "${PROJECT_PATH}/plugins/realm" && ./scripts/init.sh $( [ "$REALM_E25_DEV" = "true" ] && echo "e25" || echo "" ); cd -;
    echo -e "\033[0;34mInstalling composer for Realm...\033[0m"
    composer install --working-dir="${PROJECT_PATH}/plugins/realm"
    if  [ ! -d "${PROJECT_PATH}/plugins/realm/node_modules" ]; then
        echo -e "\033[0;34mInstalling node modules in Realm...\033[0m"
        #sudo chmod 777 "${PROJECT_PATH}/plugins/realm"
        npm install --prefix "${PROJECT_PATH}/plugins/realm"
        npm run build --prefix "${PROJECT_PATH}/plugins/realm"
    fi
fi

#Berg child theme installation
if [ -d "${PROJECT_PATH}/themes/berg-theme-child" ] && [ ! -d "${PROJECT_PATH}/themes/berg-theme-child/assets/node_modules" ]; then
    echo -e "\033[0;34mInstalling node modules in Berg Theme Child...\033[0m"
    sudo chmod 777 "${PROJECT_PATH}/themes/berg-theme-child/assets"
    sudo chmod 777 -R "${PROJECT_PATH}/themes/berg-theme-child/assets/json" "${PROJECT_PATH}/themes/berg-theme-child/assets/scss"
    npm install --prefix "${PROJECT_PATH}/themes/berg-theme-child/assets"
    npm run ng --prefix "${PROJECT_PATH}/themes/berg-theme-child/assets"
    npm run build --prefix "${PROJECT_PATH}/themes/berg-theme-child/assets"

    #Installing husky on the project root
    if [ -d "${PWD}/.git" ]
      then
        echo -e "\033[0;34mSetting up Husky...\033[0m"
        cd "${PROJECT_PATH}/themes/berg-theme-child/assets"
        npm run husky:root
        cd "${PROJECT_ROOT}"
    fi
fi

echo -e "\033[0;34mProject setup is completed. Please visit the site at http://${VIRTUAL_HOST}\033[0m"
