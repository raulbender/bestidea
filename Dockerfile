FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    unzip \
    && docker-php-ext-install pdo pdo_mysql sockets \
    && pecl install redis \
    && docker-php-ext-enable redis sockets

WORKDIR /var/www/html

# Copia os arquivos do projeto para o container
COPY . .

# Expõe a porta
EXPOSE 8080

CMD ["./rr", "serve", "-c", ".rr.yaml"]