FROM php:apache

# Mysqli extension
RUN docker-php-ext-install mysqli

# Instala o Composer
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Habilita o módulo Apache mod_rewrite
RUN a2enmod rewrite

# Reinicia o Apache para aplicar as alterações
RUN service apache2 restart