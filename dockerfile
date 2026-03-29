FROM php:8.2-apache

# 1. Instalar dependencias del sistema y preparar Node.js (versión 20)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_mysql gd zip

# 2. Habilitar mod_rewrite de Apache para las rutas de Laravel
RUN a2enmod rewrite

# 3. Establecer el directorio de trabajo
WORKDIR /var/www/html

# 4. Copiar todo el código del proyecto al contenedor
COPY . /var/www/html

# 5. Instalar Composer y las dependencias de PHP (Backend)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# 6. Instalar dependencias de Node.js y compilar Vite/Tailwind (Frontend)
RUN npm install
RUN npm run build

# 7. Configurar permisos cruciales para que Laravel pueda escribir archivos (logs, cache)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Apuntar el DocumentRoot de Apache a la carpeta "public" de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 9. Ajustar Apache para que escuche el puerto dinámico que asigna Railway
RUN echo "Listen \${PORT}" > /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf

# 10. Configurar la variable de entorno del puerto por defecto
ENV PORT=80
EXPOSE $PORT