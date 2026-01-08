# =========================
# 1️⃣ Base image: PHP 8.2 with Apache
# =========================
FROM php:8.2-apache

# =========================
# 2️⃣ Install system dependencies and PHP extensions
# =========================
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libzip-dev \
    curl \
    && docker-php-ext-install pdo pdo_pgsql zip

# =========================
# 3️⃣ Enable Apache mod_rewrite (needed for Laravel routing)
# =========================
RUN a2enmod rewrite

# =========================
# 4️⃣ Set working directory
# =========================
WORKDIR /var/www/html

# =========================
# 5️⃣ Copy all project files
# =========================
COPY . .

# =========================
# 6️⃣ Set Apache DocumentRoot to Laravel's public folder
# =========================
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# =========================
# 7️⃣ Install Composer
# =========================
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# =========================
# 8️⃣ Install Laravel PHP dependencies
# =========================
RUN composer install --no-dev --optimize-autoloader

# =========================
# 9️⃣ Set folder permissions for storage and cache
# =========================
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# =========================
# 🔹 Optional: set environment file permissions
# =========================
# RUN chmod 644 /var/www/html/.env

# =========================
# 10️⃣ Expose port 80
# =========================
EXPOSE 80

# =========================
# 11️⃣ Start Apache in foreground
# =========================
CMD ["apache2-foreground"]
