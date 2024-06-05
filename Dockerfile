FROM php:8.1-fpm

RUN apt-get update
RUN apt-get install -y \
    curl \
    git \
    zip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd


# Install Xdebug
RUN pecl install xdebug-3.1.5 && docker-php-ext-enable xdebug
    
# # add host.docker.internal to /etc/hosts
# RUN echo `ip -4 route list match 0/0 | awk '{print $3 " host.docker.internal"}' >> /etc/hosts`

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Vue CLI globally as root
RUN npm install -g @vue/cli

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

USER "1000:1000"

# Install JS dependencies and build assets
WORKDIR /var/www

# Copy dist directory to Docker container
#COPY ./dist /var/www/public

ENTRYPOINT [ "php-fpm" ]
