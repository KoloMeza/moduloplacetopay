FROM php:7.0-apache

LABEL maintainer="simon.godoy@placetopay.com"
LABEL php_version="7.0"
LABEL magento_version="2.0.18"
LABEL description="Magento 2.0.18 with PHP 7.0"

ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}
ENV MAGENTO_VERSION 2.0.18
ENV INSTALL_DIR /var/www/html
ENV COMPOSER_HOME /var/www/.composer/

RUN set -xe; \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    groupadd -g ${PGID} magendock && \
    useradd -u ${PUID} -g magendock -m magendock -G www-data && \
    usermod -p "*" magendock -s /bin/bash

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer
COPY auth.json $COMPOSER_HOME

RUN requirements="cron libfreetype6-dev libjpeg62-turbo-dev libpng-dev libmcrypt-dev libmcrypt4 libcurl3-dev libicu-dev libxslt1-dev unzip" \
    && apt-get update \
    && apt-get install -y $requirements \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && docker-php-ext-install mcrypt \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install zip \
    && docker-php-ext-install intl \
    && docker-php-ext-install xsl \
    && docker-php-ext-install soap \
    && docker-php-ext-install bcmath \
    && apt-get purge --auto-remove -y

RUN chsh -s /bin/bash www-data

RUN cd /tmp && \
  curl https://codeload.github.com/magento/magento2/tar.gz/$MAGENTO_VERSION -o $MAGENTO_VERSION.tar.gz && \
  tar xvf $MAGENTO_VERSION.tar.gz && \
  mv magento2-$MAGENTO_VERSION/* magento2-$MAGENTO_VERSION/.htaccess $INSTALL_DIR

RUN chown -R www-data:www-data /var/www
RUN su www-data -c "cd $INSTALL_DIR && composer install"
RUN su www-data -c "cd $INSTALL_DIR && composer require dnetix/redirection"
RUN su www-data -c "cd $INSTALL_DIR && composer config repositories.magento composer https://repo.magento.com/"

RUN cd $INSTALL_DIR \
    && find . -type d -exec chmod 770 {} \; \
    && find . -type f -exec chmod 660 {} \; \
    && chmod u+x bin/magento

COPY ./install-magento /usr/local/bin/install-magento
RUN chmod +x /usr/local/bin/install-magento

COPY ./install-sampledata /usr/local/bin/install-sampledata
RUN chmod +x /usr/local/bin/install-sampledata

RUN a2enmod rewrite
RUN echo "memory_limit=2048M" > /usr/local/etc/php/conf.d/memory-limit.ini

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR $INSTALL_DIR

ADD crontab /etc/cron.d/magento2-cron
RUN chmod 0644 /etc/cron.d/magento2-cron \
    && crontab -u www-data /etc/cron.d/magento2-cron
