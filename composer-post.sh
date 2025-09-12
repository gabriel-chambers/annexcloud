#!/bin/sh

# When running from the root of a docker project
if [ -f "./docker/.env" ]
then
    export $(cat ./docker/.env | sed 's/#.*//g' | xargs)
fi

# When runnning inside from "tmp" folder of a docker project
if [ -f "./../docker/.env" ]
then
    export $(cat ./../docker/.env | sed 's/#.*//g' | xargs)
fi

# If project doesn't get setup with docker and is a realm project,
# set below value to "true" manually
if [ -z ${IS_A_REALM_PROJECT+x} ];
then
  IS_A_REALM_PROJECT=false
fi

# Move downloaded Wordpress content from "wp" folder to root
echo "\e[32mMoving content of \"wp\" folder into the root folder\e[m"
mv wp/* .

# Remove "wp"
echo "\e[32mRemoving \"wp\" folder\e[m"
rm -rf wp

# Get "wp-config-sample.php" from ".git" to prevent overwriting changes
if [ -d ".git" ]
then
  echo "\e[32mResetting \"wp-config-sample.php\" if available in GIT repository\e[m"
  set +e # Prevent failing command if file does not exists in the repository
  git checkout wp-config-sample.php || true
  set -e
fi

# Clone "berg-theme-child" if not available
if [ ! -d "wp-content/themes/berg-theme-child" ]
then
  echo "\e[32mClonning \"berg-theme-child\"\e[m"
  git clone git@gitlab.com:e25berg/berg-theme-child.git wp-content/themes/berg-theme-child
  cd wp-content/themes/berg-theme-child
  git checkout main
  latestTag=$(git describe --tags)
  echo "\e[32mChecking out latest tag:${latestTag}\e[m"
  git checkout ${latestTag}
  rm -rf .git
  cd ../../../
fi

# Clone "facetwp" extension to child theme if not available
if [ ! -d "wp-content/themes/berg-theme-child/inc/facetwp" ]
then
  echo "\e[32mClonning \"facetwp\" extension\e[m"
  git clone git@gitlab.com:e25berg/brc-facetwp.git wp-content/themes/berg-theme-child/inc/facetwp
  cd wp-content/themes/berg-theme-child/inc/facetwp
  git checkout main
  facetwpTag=$(git describe --tags)
  echo "\e[32mChecking out latest tag:${facetwpTag}\e[m"
  git checkout ${facetwpTag}
  rm -rf .git
  echo "\e[32mMoving facetwp custom JS files into \"assets\" folder\e[m"
  mkdir -p ../../assets/js/berg
  mv -n js/* ../../assets/js/berg/
  rm -rf js
  cd ../../../../../
fi


# Clone "realm" if not available
if [ "$IS_A_REALM_PROJECT" = true ] && [ ! -d "wp-content/plugins/realm" ]
then
  echo "\e[32mClonning \"realm\"\e[m"
  git clone git@gitlab.com:e25realm/realm.git wp-content/plugins/realm
  cd wp-content/plugins/realm
  git checkout main
  latestTag=$(git describe --tags)
  echo "\e[32mChecking out latest tag:${latestTag}\e[m"
  git checkout ${latestTag}
  rm -rf .git
  cd ../../../
fi
