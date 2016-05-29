FROM code4hr/hercules-dockerfile-base

# Copy composer files into the app directory.
COPY composer.json composer.lock ./

# Install dependencies with Composer.
# --prefer-source fixes issues with download limits on Github.
# --no-interaction makes sure composer can run fully automated
RUN composer install --prefer-source --no-interaction

# copy in source files
RUN echo "date.timezone = 'America/New_York'" >> /usr/local/etc/php/php.ini
COPY apache.conf /etc/apache2/sites-available/
RUN a2ensite apache
RUN a2enmod rewrite
COPY . /var/www/html

# you can replace this at runtime for dev/test
# otherwise it defaults to prod ES
ENV ELASTICSEARCH_URL http://hercules.code4hr.org:33366/

# need to fill this with proper key at runtime
ENV GREATSCHOOLS_API_KEY examplekey

EXPOSE 80
CMD ["apache2-foreground"]
