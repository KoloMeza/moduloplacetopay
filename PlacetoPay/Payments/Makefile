#!/bin/bash

SERVICE = web
SERVICE_DB = db
CONTAINER = magento_module
CONTAINER_DB = magento_module_db

.PHONY: up
up:
	docker-compose up -d
	docker exec -it $(CONTAINER) service cron start

.PHONY: stop
stop:
	docker-compose stop
	docker exec -it $(CONTAINER) service cron stop

.PHONY: down
down:
	docker-compose down

.PHONY: bash
bash:
	docker-compose exec -u www-data $(SERVICE) bash

.PHONY: mysql
mysql:
	docker exec -it $(CONTAINER) mysql --user=root --password=root magento

.PHONY: build-magento-20
build-magento-20: rebuild-magento-20

.PHONY: rebuild-magento-20
rebuild-magento-20: down
	docker-compose -f docker-compose.yml -f docker-compose-magento-20.yml up -d --build
	docker exec -it $(CONTAINER) service cron start

.PHONY: build-magento-21
build-magento-21: rebuild-magento-21

.PHONY: rebuild-magento-21
rebuild-magento-21: down
	docker-compose -f docker-compose.yml -f docker-compose-magento-21.yml up -d --build
	docker exec -it $(CONTAINER) service cron start

.PHONY: build-magento-22
build-magento-22: rebuild-magento-22

.PHONY: rebuild-magento-22
rebuild-magento-22: down
	docker-compose -f docker-compose.yml -f docker-compose-magento-22.yml up -d --build
	docker exec -it $(CONTAINER) service cron start

.PHONY: build-magento-23
build-magento-23: rebuild-magento-23

.PHONY: rebuild-magento-23
rebuild-magento-23: down
	docker-compose -f docker-compose.yml -f docker-compose-magento-23.yml up -d --build
	docker exec -it $(CONTAINER) service cron start

.PHONY: install-magento
install-magento:
	docker exec -it $(CONTAINER) install-magento

.PHONY: install-sampledata
install-sampledata:
	docker exec -it $(CONTAINER) install-sampledata
