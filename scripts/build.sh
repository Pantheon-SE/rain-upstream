#!/bin/bash

SCRIPT_DIR=$(cd $(dirname "$0") && pwd -P)
BASE_DIR=$(cd ${SCRIPT_DIR}/.. && pwd -P)

cd ${BASE_DIR}

COMPOSER_INSTALLED=$(command -v composer)

if [ -z "${COMPOSER_INSTALLED}" ]; then
  echo "Composer ( https://getcomposer.org/) not found, please install."
  exit 1;
fi

if [ ! -d ${BASE_DIR}/vendor ]; then
  composer install
fi

${BASE_DIR}/bin/robo --ansi --load-from ${BASE_DIR}/scripts site:build
