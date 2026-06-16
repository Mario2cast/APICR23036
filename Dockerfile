FROM php:8.2-apache

# Se instalan las extensiones necesarias para que PHP pueda conectarse a MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Se copia Composer dentro del contenedor para instalar Slim y sus dependencias
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Se activa mod_rewrite para que funcionen las rutas de Slim
RUN a2enmod rewrite

# Se define la carpeta de trabajo dentro del servidor
WORKDIR /var/www/html

# Se copian todos los archivos de la API al contenedor
COPY . .

# Se instalan las dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Se configura Apache para que use la carpeta public como raíz del proyecto
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Se evita una advertencia común de Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]