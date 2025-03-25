FROM php:8.4.5-fpm-bookworm
ENV PATH="$PATH:/home/application/bin"
RUN useradd -u 1000 application
RUN apt-get update \
    && apt-get install -y \
        vim \
        net-tools \
        git \
        zip \
    && pecl install opentelemetry && docker-php-ext-enable opentelemetry \
    && pecl install xdebug && docker-php-ext-enable xdebug \
    && pecl install redis && docker-php-ext-enable redis \
    && mkdir -p /home/application/bin \
    && chown -R application /home/application \
    && echo 'xdebug.mode=debug' >>/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.start_with_request=trigger' >>/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.discover_client_host=On' >>/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && printf '#!/usr/bin/env bash\nexport PHP_IDE_CONFIG && php -d xdebug.mode=debug -d xdebug.start_with_request=Yes -d xdebug.client_host=$(docker-host) $@' > /home/application/bin/xphp \
    && printf '#!'"/usr/bin/env bash\necho -n \$(route -n | grep 'UG[ \t]' | awk '{print \$2}')" > /home/application/bin/docker-host \
    && printf '#!/usr/bin/env bash\nXDEBUG_FILE=$(php -i | grep -Eo ^.+xdebug.ini)\nFILE_CONTENT=$(cat $XDEBUG_FILE)\n[[ "${FILE_CONTENT:0:1}" == "#" ]] &&  echo -n "${FILE_CONTENT:1}" > $XDEBUG_FILE && echo "Enabled"' > /home/application/bin/xdebug-enable \
    && printf '#!/usr/bin/env bash\nXDEBUG_FILE=$(php -i | grep -Eo ^.+xdebug.ini)\nFILE_CONTENT=$(cat $XDEBUG_FILE)\n[[ "${FILE_CONTENT:0:1}" != "#" ]] &&  echo -n "#${FILE_CONTENT}" > $XDEBUG_FILE && echo "Disabled"' > /home/application/bin/xdebug-disable \
    && chmod +x /home/application/bin/* \
    && chmod 777 $(php -i | grep -Eo ^.+xdebug.ini)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer