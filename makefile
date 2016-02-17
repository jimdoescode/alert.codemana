.PHONY: client server-migrate server-install

client:
	cd ./client && npm run build-dev-css && npm run build-dev-js

server: server-install server-migrate

# This expects that the user has already performed a composer install and has php installed globally
server-migrate:
	cd ./server && php vendor/bin/phinx migrate --configuration=src/Alerts/phinx.php

# This expects that the user has composer installed globally.
server-install:
	cd ./server && composer install
