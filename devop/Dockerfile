FROM php:8.1-apache

# Update und Upgrade der Paketlisten sowie Installieren von Hilfswerkzeugen
RUN apt-get update && apt-get upgrade -y && apt-get install -y zip unzip openssh-client git rsync

# Installation der PHP-Erweiterungen
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Aktivieren des Apache-Moduls rewrite
RUN a2enmod rewrite

# SSH-Key
RUN mkdir -p ~/.ssh && ln -s /run/secrets/ssh_key ~/.ssh/id_rsa

# Installieren von Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer