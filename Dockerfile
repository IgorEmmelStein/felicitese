FROM php:8.2-apache

# Instala extensões necessárias do PHP (PDO MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Habilita o módulo rewrite do Apache
RUN a2enmod rewrite

# Ajusta configurações de upload no PHP
RUN echo "upload_max_filesize = 20M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 25M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 128M" >> /usr/local/etc/php/conf.d/uploads.ini

# Define o diretório de trabalho
WORKDIR /var/www/html

# Cria estrutura inicial de uploads e ajusta permissões
RUN mkdir -p /var/www/html/uploads/imagens /var/www/html/uploads/documentos

