#!/bin/bash

currentWorkingDir=$(pwd)
tmpDir="${currentWorkingDir}/tmp"

# Create "tmp" folder if not exists
if [ ! -d "${tmpDir}" ]
then
  mkdir "${tmpDir}"
fi

set -x

expectedNodeVersion="14.17.1"
currentNodeVersion=$(node -v)

# Install node version 14.17.1 if installed version doesn't match
if [ "$currentNodeVersion" != "v$expectedNodeVersion" ]
then
  # Download and install "nvm" if not installed
  if [ ! command -v nvm &> /dev/null ]
  then
    curl -sL https://raw.githubusercontent.com/creationix/nvm/v0.35.3/install.sh -o "${tmpDir}/install_nvm.sh"
    bash "${tmpDir}/install_nvm.sh"
    source ~/.bashrc
  fi
  nvm install "${expectedNodeVersion}"
  nvm use "${expectedNodeVersion}"
  node -v
fi

# Commented by Ravisha 2022-02-28
# rm -rf wp-content/plugins
# rm -rf wp-content/themes

# Download composer 2.2.7
wget https://getcomposer.org/download/2.2.7/composer.phar -P "${tmpDir}"
alias composer="php -d allow_url_fopen=On ${tmpDir}/composer.phar"

composer up --no-interaction

# Remove temp folder
rm -rf "${tmpDir}"

npm --prefix "${currentWorkingDir}/wp-content/themes/berg-theme/assets" i && npm --prefix "${currentWorkingDir}/wp-content/themes/berg-theme/assets" run ng && npm --prefix "${currentWorkingDir}/wp-content/themes/berg-theme/assets" run build

npm --prefix "${currentWorkingDir}/wp-content/themes/berg-theme-child/assets" i && npm --prefix "${currentWorkingDir}/wp-content/themes/berg-theme-child/assets" run ng && npm --prefix "${currentWorkingDir}/wp-content/themes/berg-theme-child/assets" run build

npm --prefix "${currentWorkingDir}/wp-content/plugins/berg" i && npm --prefix "${currentWorkingDir}/wp-content/plugins/berg" run build
