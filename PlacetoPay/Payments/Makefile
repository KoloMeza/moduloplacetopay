#!/bin/bash

SERVICE = web
SERVICE_DB = db
CONTAINER_MG = magento_module
CONTAINER_DB = magento_module_db
CURRENT_FOLDER=$(shell pwd)
UID=$(shell id -u)
MODULE_NAME=magento2-placetopay

.PHONY: up
up:
	docker-compose up -d
	docker exec -it $(CONTAINER_MG) service cron start

.PHONY: stop
stop:
	docker-compose stop
	docker exec -it $(CONTAINER_MG) service cron stop

.PHONY: down
down:
	docker-compose down

.PHONY: bash
bash:
	docker-compose exec -u www-data $(SERVICE) bash

.PHONY: mysql
mysql:
	docker exec -it $(CONTAINER_MG) mysql --user=root --password=root magento

.PHONY: build-magento-20
build-magento-20: rebuild-magento-20

.PHONY: rebuild-magento-20
rebuild-magento-20: down
	docker-compose -f docker-compose.yml -f docker-compose-magento-20.yml up -d --build
	docker exec -it $(CONTAINER_MG) service cron start

.PHONY: build-magento-21
build-magento-21: rebuild-magento-21

.PHONY: rebuild-magento-21
rebuild-magento-21: down
	docker-compose -f docker-compose.yml -f docker-compose-magento-21.yml up -d --build
	docker exec -it $(CONTAINER_MG) service cron start

.PHONY: build-magento-22
build-magento-22: rebuild-magento-22

.PHONY: rebuild-magento-22
rebuild-magento-22: down
	docker-compose -f docker-compose.yml -f docker-compose-magento-22.yml up -d --build
	docker exec -it $(CONTAINER_MG) service cron start

.PHONY: build-magento-23
build-magento-23: rebuild-magento-23

.PHONY: rebuild-magento-23
rebuild-magento-23: down
	docker-compose -f docker-compose.yml -f docker-compose-magento-23.yml up -d --build
	docker exec -it $(CONTAINER_MG) service cron start

.PHONY: install-magento
install-magento:
	docker exec -it $(CONTAINER_MG) install-magento

.PHONY: install-sampledata
install-sampledata:
	docker exec -it $(CONTAINER_MG) install-sampledata

compile:
	$(eval MODULE_NAME_VR=$(MODULE_NAME)$(PLUGIN_VERSION))
	@touch ~/Downloads/magento2-placetopay-test \
        && rm -Rf ~/Downloads/magento2-placetopay* \
        && cp -pr $(CURRENT_FOLDER) ~/Downloads/magento2-placetopay \
        && cd ~/Downloads/magento2-placetopay \
        && find ~/Downloads/magento2-placetopay/ -type d -name ".git*" -exec rm -Rf {} + \
        && rm -Rf ~/Downloads/magento2-placetopay/.git* \
        && rm -Rf ~/Downloads/magento2-placetopay/.idea \
        && rm -Rf ~/Downloads/magento2-placetopay/Makefile \
        && rm -Rf ~/Downloads/magento2-placetopay/.env* \
        && rm -Rf ~/Downloads/magento2-placetopay/env \
        && rm -Rf ~/Downloads/magento2-placetopay/.docker* \
        && rm -Rf ~/Downloads/magento2-placetopay/docker* \
        && rm -Rf ~/Downloads/magento2-placetopay/*.md \
        && rm -Rf ~/Downloads/magento2-placetopay/*.txt \
        && rm -Rf ~/Downloads/magento2-placetopay/vendor/* \
        && rm -Rf ~/Downloads/magento2-placetopay/vendor \
        && cd ~/Downloads \
        && zip -r -q -o $(MODULE_NAME_VR).zip magento2-placetopay \
        && chown $(UID):$(UID) $(MODULE_NAME_VR).zip \
        && chmod 644 $(MODULE_NAME_VR).zip \
        && rm -Rf ~/Downloads/magento2-placetopay
	@echo "Compile file complete: ~/Downloads/$(MODULE_NAME_VR).zip"
