FROM php:8.2-cli

# 1. Instala as dependências do sistema e extensões PHP necessárias
# (Adicionamos o 'git' aqui porque o Composer precisa dele para baixar pacotes)
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_mysql sockets \
    && pecl install redis \
    && docker-php-ext-enable redis sockets

# 2. Instala o Composer trazendo o binário oficial direto da imagem do Composer
# (Essa é uma manobra sênior de multi-stage build, super limpa e rápida!)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Define o diretório de trabalho onde o app vai morar
WORKDIR /var/www/html

# 4. Copia os arquivos do seu repositório para dentro do container
COPY . .

# 5. Executa o comando para instalar as dependências do Composer em modo de produção
# (--no-dev remove pacotes de teste desnecessários, deixando o container leve)
RUN composer install --no-dev --prefer-dist --no-scripts --no-progress --optimize-autoloader

# 6. Agora que a pasta vendor existe, baixa o binário do RoadRunner no lugar certo
RUN php vendor/bin/rr get-binary

# 7. Expõe a porta que o RoadRunner vai escutar
EXPOSE 8080

# 8. Comando único de inicialização
CMD php migrate.php && ./rr serve -c .rr.yaml