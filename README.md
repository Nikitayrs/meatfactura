docker compose up --build -d установка всех образов и запуск конетйнеров

docker compose exec php-cli bash # переходим в контейнер php-cli и заходим в bash
composer install

SHOW GRANTS FOR user;

docker compose exec node npm install
docker compose exec npm run build

В Postam -> Body используется параметр csrf_token, его мы получаем в /meatfactura_project/core/Application.php
в методе generateCsrfToken()

Создание БД

CREATE DATABASE meatfactura
    WITH
    OWNER = development
    ENCODING = 'UTF8'
    LC_COLLATE = 'en_US.utf8'
    LC_CTYPE = 'en_US.utf8'
    LOCALE_PROVIDER = 'libc'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1
    IS_TEMPLATE = False;
USE reviews_db;

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    phone CHARACTER VARYING(255) UNIQUE,
    name CHARACTER VARYING(30) NOT NULL,
    address CHARACTER VARYING(255),
    password TEXT NOT NULL,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);

CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = current_timestamp;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER users_updated_at_trigger
BEFORE UPDATE ON users
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE IF NOT EXISTS phoneVerifications (
    id SERIAL PRIMARY KEY,
    phone VARCHAR(255) NOT NULL UNIQUE,
    code VARCHAR(10) NOT NULL,
    expires_at TIMESTAMPTZ NOT NULL,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER phoneVerifications_updated_at_trigger
BEFORE UPDATE ON phoneVerifications
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price NUMERIC(10, 2) NOT NULL,
    category VARCHAR(100) NOT NULL,
    availability INTEGER NOT NULL DEFAULT(0),
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER products_updated_at_trigger
BEFORE UPDATE ON products
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

INSERT INTO products (name, description, price, category, availability)
VALUES
-- Категория: Свежее мясо (7 продуктов)
('Говяжья вырезка', 'Нежное мраморное мясо высшего сорта', 950.00, 'Свежее мясо', 3),
('Свиная лопатка', 'Идеальна для тушения и запекания', 450.00, 'Свежее мясо', 6),
('Баранина на кости', 'Фермерская баранина для плова', 780.00, 'Свежее мясо', 5),
('Куриные грудки', 'Филе без кожи, охлаждённое', 320.00, 'Свежее мясо', 8),
('Индейка бедро', 'Сочное мясо для гриля', 410.00, 'Свежее мясо', 9),
('Телячий окорок', 'Нежное мясо для эскалопов', 1200.00, 'Свежее мясо', 3),
('Утиные ножки', 'Для конфи и запекания', 530.00, 'Свежее мясо', 5),

-- Категория: Колбасные изделия (8 продуктов)
('Сервелат', 'Вяленая колбаса премиум класса', 680.00, 'Колбасные изделия', 7),
('Докторская', 'Классическая варёная колбаса', 290.00, 'Колбасные изделия', 8),
('Салями Пиканте', 'Острая итальянская колбаса', 870.00, 'Колбасные изделия', 8),
('Кровяная колбаса', 'Традиционная с гречневой крупой', 360.00, 'Колбасные изделия', 5),
('Ветчина в/к', 'Варено-копчёная из свиной корейки', 620.00, 'Колбасные изделия', 4),
('Чоризо', 'Испанская копчёная колбаса с паприкой', 710.00, 'Колбасные изделия', 11),
('Салями Чиабатто', 'С итальянскими травами', 890.00, 'Колбасные изделия', 10),
('Куриные сосиски', 'Нежные с добавлением сливок', 240.00, 'Колбасные изделия', 15);

CREATE TABLE IF NOT EXISTS prod_order (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL,
    status TEXT DEFAULT 'new' NOT NULL,
    price NUMERIC(10, 2) NOT NULL,
    comment TEXT,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TRIGGER prod_order_updated_at_trigger
BEFORE UPDATE ON prod_order
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();

CREATE TABLE IF NOT EXISTS orders (
    id SERIAL PRIMARY KEY,
    prod_order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL DEFAULT(0),
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (prod_order_id) REFERENCES prod_order(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TRIGGER orders_updated_at_trigger
BEFORE UPDATE ON orders
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();