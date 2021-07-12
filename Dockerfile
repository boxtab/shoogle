FROM php:7.3-fpm

ARG USER
ARG UID

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev \
    libfreetype6 \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    netcat

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g $UID $USER
RUN useradd -u $UID -ms /bin/bash -g $USER $USER

# Copy existing application directory permissions
COPY --chown=$USER:$USER . .

# Change current user
USER $USER

# Expose port 9000 and start php-fpm server
EXPOSE 9000

#RUN chmod +x ./init.sh
#CMD ["php-fpm"]
ENTRYPOINT ["./init.sh"]
