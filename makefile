.PHONY: client

client:
	cd ./client && npm run build-dev-css && npm run build-dev-js

