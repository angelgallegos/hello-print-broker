#!/usr/bin/env bash

current_dir=$(cd $(dirname $0) && pwd)
root_dir=${current_dir}/..

# stop on error
set -e

cd ${root_dir}
# build images for that php version
scp composer.json composer.lock cli-config.php operations/docker/php-apache