FROM php:8.0-cli

# Installing tools & libraries
RUN apt-get -y update && apt-get -y upgrade
RUN apt-get -y install --fix-missing git unzip pkg-config \
    librdkafka-dev libcurl4-openssl-dev libssl-dev #mc nano

# Installing PHP extensions
#RUN docker-php-ext-configure intl
RUN docker-php-ext-install pcntl #intl
# Installing PECL extensions
#RUN pecl config-set php_ini /etc/php.ini
RUN pecl install rdkafka-5.0.2 && docker-php-ext-enable rdkafka

# Cleanup after install
RUN docker-php-source delete \
    && apt-get -y autoremove --purge \
    && apt-get -y autoclean \
    && apt-get -y clean

# Installing composer
RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

# Project data
WORKDIR /app
COPY . .

RUN composer install

CMD php ${BUILDER_TYPE}
