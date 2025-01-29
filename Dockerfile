# Imagen base de PHP 8.2 con Apache y Xdebug 3
FROM php:8.4-apache

# Instalar dependencias de AMQP, Xdebug, y extensiones necesarias
RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    librabbitmq-dev \
    zip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip \
    && pecl install amqp xdebug-3.4.0 \
    && docker-php-ext-enable amqp xdebug \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copiar la configuración de Xdebug
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Verificar las extensiones instaladas
RUN php -m | grep -E 'amqp|xdebug|gd|pdo|pdo_mysql|zip'

# Configuración de Xdebug
RUN echo "xdebug.mode=debug" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Habilitar módulo de Apache para el archivo .htaccess
RUN a2enmod rewrite

# Exponer el puerto 9003 para Xdebug
EXPOSE 9003

# Directorio de trabajo en el contenedor
WORKDIR /var/www/html

# Copiar archivo de configuración de Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Instalar Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Iniciar el servidor Apache y PHP
CMD ["apache2-foreground"]
