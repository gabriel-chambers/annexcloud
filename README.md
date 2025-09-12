## Docker based development environment for Wordpress native projects

**Requirements**

1.  Docker
2.  docker-compose (Comes with Docker for Windows and Mac) [Ubuntu Docker Installation Guide](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-compose-on-ubuntu-20-04)
3.  [Concurrently](https://www.npmjs.com/package/concurrently) global installation
4.  Composer version 2.5.5+ or higher
5.  Node 18.16.0 or higher and npm 9 or higher
6.  PHP 8.0 higher
7.  Make sure there's a key file named id_rsa in ~/.ssh whereas the relavant id_rsa.pub is added in gitlab with required repository access.

NOTE: Above steps 1. and 2. are only required if you are using docker based environment

**Steps to initialize environment**

1. Create the `.env` file in the docker directory(annex-cloud/docker) by cloning the sample file `.env.example`

2. Open `.env` and configure following parameters accordingly

   - `PROJECT_NAME` Name of the project that needs to be setup. Ex: annexcloud

   - `VIRTUAL_HOST` Domain name that needs to be set in the dev environment. Ex: annexcloud.local

   - `S3_UPLOADS_BUCKET` Provide the s3 uploads bucket name (Optional)

   - `S3_UPLOADS_REGION` Provide the region (Optional)

   - `S3_UPLOADS_KEY` Provide the uploads key (Optional)

   - `S3_UPLOADS_SECRET` Provide the uploads secrets (Optional)

   - `S3_UPLOADS_USE_INSTANCE_PROFILE` Provide either true or false (Optional)

   - `DB_HOST` Provide the host IP of the database

   - `MYSQL_DATABASE` Provide the database name

   - `MYSQL_USER` Provide the username

   - `MYSQL_PASSWORD`Provide the password

   - `MYSQL_ROOT_PASSWORD` (Optional)

3. To setup the project run the commands given below

   - To download and setup the site, run `./setup.sh`

   - To destroy the docker containers created `./destroy.sh`

4. Please monitor the terminal output to see the status of each command
5. Please use bellow command to apply js or scss files in child thems folder
   - `npm run ng` (follow the current development)
   - `npm run watch`

**Steps to initialize if you need to build the code manually**

- Add the host record to etc/hosts ex: 127.0.0.1 annexcloud.local
- Run below build commands

```
- cd ./ (Root directory)
   cp ./docker/wp-config-sample.php ./wp-config.php

- cd ./ (Root directory)
   composer install

- cd ./wp-content/themes/berg-theme-child/assets
   npm i
   npm run ng
   npm run build

- cd ./wp-content/plugins/berg-custom
   npm i
   npm run build

```

#### XDebug usage

1. Install [PHP Debug](https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug) VSCode extension by XDebug.
2. Set appropriate `DOCKER_HOST_NAME` value in `.env` (`host.docker.internal` value supports for Mac, Windows and Ubuntu >=20.10. For other OS use `172.17.0.1` or an appropriate value)
3. Run "Listen for Xdebug" task from VSCode debugger.
4. Add a breakpoint
5. Append `?XDEBUG_TRIGGER=1`(any non empty string can be used instead of "1") to the URL and reload the page.
