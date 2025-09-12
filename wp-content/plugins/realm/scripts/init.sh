#!/usr/bin/env bash

MODE=dev
if [ "$1" = e25 ]; then MODE=e25; fi

COMPOSER_PATH=composer/${MODE}/composer.${MODE}.json
COMPOSER_LOCK_PATH=composer/${MODE}/composer.${MODE}.lock

ln -sf ${COMPOSER_PATH} ./composer.json
ln -sf ${COMPOSER_LOCK_PATH} ./composer.lock

# Optionally run php setup script here.
echo 'Done'
