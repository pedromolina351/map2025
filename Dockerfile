# Base oficial PHP 8.2-FPM (Debian Bookworm)
FROM php:8.2-fpm

# Configuración no interactiva y puerto de exposición
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV PORT=8080

# ---------------------------
# Paquetes del sistema
# ---------------------------
# - Sin apt-transport-https (obsoleto)
# - Sin libodbc1 (no existe; unixodbc ya trae libodbc2)
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    curl \
    gnupg2 \
    lsb-release \
    openssl \
    tzdata \
    git \
    zip \
    unzip \
    nginx \
    build-essential \
    autoconf \
    libssl-dev \
    # ODBC / Kerberos
    unixodbc \
    unixodbc-dev \
    libgssapi-krb5-2 \
    # Dependencias para PHP GD/ZIP
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zlib1g-dev \
    libwebp-dev \
    libxpm-dev \
    libzip-dev \
 && rm -rf /var/lib/apt/lists/*

# ---------------------------
# Extensiones PHP (zip, gd, mysqli, pdo_mysql, bcmath)
# ---------------------------
RUN docker-php-ext-configure zip \
 && docker-php-ext-install -j"$(nproc)" zip \
 && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
 && docker-php-ext-install -j"$(nproc)" gd mysqli pdo pdo_mysql bcmath

# ---------------------------
# Repositorio Microsoft (Debian 12) + msodbcsql18 / mssql-tools18
# ---------------------------
RUN set -eux; \
    install -d /usr/share/keyrings; \
    curl -fsSL https://packages.microsoft.com/keys/microsoft.asc \
      | gpg --dearmor -o /usr/share/keyrings/microsoft.gpg; \
    echo "deb [arch=amd64 signed-by=/usr/share/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" \
      > /etc/apt/sources.list.d/mssql-release.list; \
    apt-get update; \
    ACCEPT_EULA=Y apt-get install -y --no-install-recommends msodbcsql18 mssql-tools18; \
    rm -rf /var/lib/apt/lists/*

# ---------------------------
# Extensiones SQL Server (PECL)
# ---------------------------
RUN pecl install sqlsrv pdo_sqlsrv \
 && docker-php-ext-enable sqlsrv pdo_sqlsrv

# ---------------------------
# Configuración PHP-FPM y php.ini
# ---------------------------
RUN mkdir -p /var/run/php && chown www-data:www-data /var/run/php \
 && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
 && { \
      echo "memory_limit = 512M"; \
      echo "max_execution_time = 120"; \
    } >> /usr/local/etc/php/php.ini

# Certificados para curl/openssl desde PHP (opcional)
RUN curl -fsSL -o /usr/local/etc/php/conf.d/ca-certificates.crt https://curl.se/ca/cacert.pem \
 && printf 'openssl.cafile=/usr/local/etc/php/conf.d/ca-certificates.crt\n' > /usr/local/etc/php/conf.d/openssl.ini

# ---------------------------
# Node.js 22.x (Debian)
# ---------------------------
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
 && apt-get update && apt-get install -y --no-install-recommends nodejs \
 && rm -rf /var/lib/apt/lists/*

# ---------------------------
# Directorio de trabajo y código
# ---------------------------
WORKDIR /var/www
COPY . .

# Nginx (tu configuración)
COPY nginx.conf /etc/nginx/nginx.conf

# Permisos Laravel y logs Nginx (evitar 777)
RUN mkdir -p /var/log/nginx \
 && chown -R www-data:www-data /var/log/nginx \
 && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
 && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# ---------------------------
# Composer
# ---------------------------
RUN curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
 && php artisan storage:link || true

# Comprobaciones rápidas
RUN php-fpm --test || echo "PHP-FPM config check emitted warnings"; \
    php -m | grep -E 'zip|gd' || true

# ---------------------------
# Script de inicio: php-fpm + nginx
# ---------------------------
RUN printf '#!/bin/sh\nset -e\nphp-fpm -F &\nsleep 2\nexec nginx -g "daemon off;"\n' > /start.sh \
 && chmod +x /start.sh

EXPOSE ${PORT}
CMD ["/start.sh"]
