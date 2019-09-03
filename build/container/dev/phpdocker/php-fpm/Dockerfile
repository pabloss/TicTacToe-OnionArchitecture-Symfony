FROM phpdockerio/php72-fpm:latest
WORKDIR "/application"

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install php7.2-dev php-memcached php7.2-mysql php-redis php7.2-sqlite3 php-xdebug php-pear \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
    pecl update-channels; \
	rm -rf /tmp/pear ~/.pearrc;
