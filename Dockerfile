FROM php:8.2-apache

# Se instalan herramientas necesarias para que Composer pueda descargar paquetes
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Se instalan extensiones necesarias para trabajar con MySQL y archivos ZIP
RUN docker-php-ext-install pdo_mysql zip

# Se copia Composer dentro del contenedor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Se activa mod_rewrite para que Slim pueda manejar rutas como /doctores
RUN a2enmod rewrite

# Se define la carpeta principal del proyecto
WORKDIR /var/www/html

# Se copian los archivos de la API al servidor
COPY . .

# Se instalan las dependencias de Slim desde composer.json
RUN composer install --no-dev --optimize-autoloader

# Se configura Apache para usar public como carpeta raíz
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Se permite el uso de .htaccess dentro de public
RUN printf '<Directory /var/www/html/public>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n' > /etc/apache2/conf-available/public-override.conf \
    && a2enconf public-override

# Se evita una advertencia común de Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]
