.PHONY: client

client:
	cd ./client && pwd && npm run build-dev-css && npm run build-dev-js

