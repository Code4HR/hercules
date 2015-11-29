FROM php:apache

RUN \
  apt-get update && \
  DEBIAN_FRONTEND=noninteractive apt-get install -y \
  ant \
  git \
  php5-curl \
  amavisd-new \
  libcurl4-gnutls-dev \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libmcrypt-dev \
  libpng12-dev \
  libbz2-dev \
  php-pear \
  curl \
  && rm -r /var/lib/apt/lists/*

# PHP Extensions
RUN docker-php-ext-install mcrypt zip bz2 mbstring \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install gd

# Memory Limit
RUN echo "memory_limit=1024M" > $PHP_INI_DIR/conf.d/memory-limit.ini

#install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
#RUN curl -sS https://getcomposer.org/installer | php
#RUN mv composer.phar /usr/local/bin/composer

# Set the WORKDIR to /app so all following commands run in /app
WORKDIR /var/www/html
#WORKDIR /app

# Copy composer files into the app directory.
COPY composer.json composer.lock ./

# Install dependencies with Composer.
# --prefer-source fixes issues with download limits on Github.
# --no-interaction makes sure composer can run fully automated
RUN composer install --prefer-source --no-interaction

# copy in source files
COPY apache.conf /etc/apache2/sites-available/
RUN a2enmod rewrite
COPY . /var/www/html
#WORKDIR /var/www/html

# run composer
#RUN php composer install

EXPOSE 80
CMD ["apache2-foreground"]
