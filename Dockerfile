FROM php:8.2-apache

# Se instalan herramientas necesarias para que Composer pueda descargar dependencias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Se instalan las extensiones necesarias para conectar PHP con MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Se copia Composer dentro del contenedor
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Se activa mod_rewrite para que funcionen las rutas de Slim
RUN a2enmod rewrite

# Se define la carpeta de trabajo del proyecto
WORKDIR /var/www/html

# Se copian los archivos de la API al servidor
COPY . .

# Se instalan las dependencias del proyecto con Composer
RUN composer install --no-dev --optimize-autoloader

# Se configura Apache para usar la carpeta public como raíz del proyecto
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Se permite el uso de .htaccess
RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Se evita una advertencia de Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]
