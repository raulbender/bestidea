FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    unzip \
    && docker-php-ext-install pdo pdo_mysql sockets \
    && pecl install redis \
    && docker-php-ext-enable redis sockets

WORKDIR /var/www/html

# Copia os arquivos do projeto para o container
COPY . .

# 1. Baixa o binário do RoadRunner para dentro do container durante o BUILD
RUN php vendor/bin/rr get-binary

# Expõe a porta
EXPOSE 8080

# 2. O comando padrão de inicialização (No formato Shell do Docker)
# O Docker nativamente vai criar o "sh -c" perfeito por baixo dos panos!
CMD php migrate.php && ./rr serve -c .rr.yaml