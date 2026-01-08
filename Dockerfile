# 1️⃣ Base image: PHP 8.2 with Apache
FROM php:8.2-apache

# 2️⃣ Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip

# 3️⃣ Enable Apache mod_rewrite (needed for Laravel routing)
RUN a2enmod rewrite

# 4️⃣ Set working directory
WORKDIR /var/www/html

# 5️⃣ Copy all project files to container
COPY . .

# ⚡ Fix Apache to serve Laravel public folder
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# 6️⃣ Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 7️⃣ Install Laravel PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# 8️⃣ Set folder permissions for storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9️⃣ Expose port 80
EXPOSE 80

# 10️⃣ Start Apache in foreground
CMD ["apache2-foreground"]
