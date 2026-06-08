FROM php:8.4-cli

# 1. Instala as dependências do sistema e extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql sockets \
    && pecl install redis \
    && docker-php-ext-enable redis sockets

# 2. Instala o Composer trazendo o binário oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Define o diretório de trabalho
WORKDIR /var/www/html

# 4. Copia os arquivos do projeto
COPY . .

# 5. Instala as dependências IGNORANDO os limites da config de plataforma antiga
# (Isso resolve o conflito entre o lock do PHP 8.4 e a trava visual do json)
RUN composer install --no-dev --prefer-dist --no-scripts --no-progress --optimize-autoloader --ignore-platform-reqs

# 6. Baixa o binário do RoadRunner
RUN php vendor/bin/rr get-binary

# 7. Expõe a porta
EXPOSE 8080

# 8. Comando único de inicialização
CMD php volt migrate && ./rr serve -c .rr.yaml