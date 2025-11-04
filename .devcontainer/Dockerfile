# Base image s PHP 8.3, Composerem a Node.js 20
FROM mcr.microsoft.com/devcontainers/php:8.3

# Instalace závislostí (MySQL client, Redis, zip, apod.)
RUN apt-get update && export DEBIAN_FRONTEND=noninteractive \
    && apt-get install -y \
        git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
        default-mysql-client redis-tools \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalace Composeru
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalace Node.js a NPM
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs

# Nastavení pracovního adresáře
WORKDIR /workspaces/${localWorkspaceFolderBasename}

# Instalace Laravel CLI (volitelné)
RUN composer global require laravel/installer

ENV PATH="$PATH:/root/.composer/vendor/bin"
