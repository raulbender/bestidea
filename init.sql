-- Cria o banco de testes se ele não existir
CREATE DATABASE IF NOT EXISTS volt_db_test;

-- Garante que o usuário root tenha acesso a ele
GRANT ALL PRIVILEGES ON volt_db_test.* TO 'root'@'%';