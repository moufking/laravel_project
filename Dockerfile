FROM php:7.4-fpm

# Arguments defined in docker-compose.yml
ARG user=thetiptopuser
ARG uid=1000
ARG  APP_PORT=8000

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache   curl \
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer



# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Install Xdebug
RUN yes | pecl install xdebug \
    && echo "zend_extension=$(find $(php-config --extension-dir) -name xdebug.so)" \
         > /usr/local/etc/php/conf.d/xdebug.ini

# Set working directory
WORKDIR /var/www

COPY  . /var/www

RUN composer install  && cp .env.example .env &&  php artisan key:generate

COPY --chown=thetiptopuser:1000 . /var/www

CMD php artisan serve --host=0.0.0.0 --port=8000

EXPOSE 8000

USER $user
