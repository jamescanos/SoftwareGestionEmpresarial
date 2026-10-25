# Se usa una imagen oficial de PHP con Apache incluido
FROM php:8.2-apache

# Se actualiza el sistema y se instalan las librerías necesarias para extensiones PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli \
    && a2enmod rewrite

# Se habilita el módulo de reescritura de Apache (útil para Laravel después)
RUN a2enmod rewrite

# Se define el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Se copia un archivo de prueba por si la carpeta 'src' está vacía
RUN echo "<?php phpinfo(); ?>" > /var/www/html/index.php

# Se asignan permisos al usuario de Apache (www-data)
RUN chown -R www-data:www-data /var/www/html