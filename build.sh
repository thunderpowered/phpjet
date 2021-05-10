#!/bin/sh

# install composer
sh ./install/composer.sh

# install dependencies
cd ./dist || exit
composer install
cd ./../ || exit

# copy a new config file
cp ./install/Config.php.build ./dist/App/Engine/Config/default/Config.php

# prepare engine files
php ./dist/web/index.php configure migrate --hard --sys -p

# well, most likely
echo "If you see this message, then everything went well. The 'dist' folder can be uploaded to your web server."
exit