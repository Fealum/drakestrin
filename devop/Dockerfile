FROM php:8.5-apache

# Update und Upgrade der Paketlisten sowie Installieren von Hilfswerkzeugen
RUN apt-get update && apt-get upgrade -y && apt-get install -y libzip-dev zip unzip openssh-client git rsync

# Installation der PHP-Erweiterungen
RUN docker-php-ext-install pdo pdo_mysql zip
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Aktivieren des Apache-Moduls rewrite
RUN a2enmod rewrite

# SSH-Key
COPY docker-entrypoint.sh /usr/local/bin/drakestrin-entrypoint
RUN chmod 755 /usr/local/bin/drakestrin-entrypoint

# Installieren von Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENTRYPOINT ["drakestrin-entrypoint"]
CMD ["apache2-foreground"]
