-- Inserir dados de teste para a interface de upload

-- Inserir usuário seller se não existir
INSERT OR IGNORE INTO users (id, name, email, email_verified_at, password, role, created_at, updated_at) 
VALUES (1, 'Vendedor Teste', 'seller@test.com', datetime('now'), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller', datetime('now'), datetime('now'));

-- Inserir perfil do seller se não existir
INSERT OR IGNORE INTO seller_profiles (id, user_id, document_type, document_number, company_name, status, created_at, updated_at)
VALUES (1, 1, 'cpf', '12345678901', 'Loja Teste', 'approved', datetime('now'), datetime('now'));

-- Inserir categoria se não existir
INSERT OR IGNORE INTO categories (id, name, slug, description, is_active, created_at, updated_at)
VALUES (1, 'Eletrônicos', 'eletronicos', 'Produtos eletrônicos diversos', 1, datetime('now'), datetime('now'));

-- Inserir produtos de teste
INSERT OR IGNORE INTO products (id, name, slug, description, short_description, price, compare_price, category_id, seller_id, sku, stock_quantity, status, weight, created_at, updated_at)
VALUES 
(1, 'Smartphone Samsung Galaxy A54', 'smartphone-samsung-galaxy-a54', 'Smartphone com tela de 6.4 polegadas, câmera tripla de 50MP + 12MP + 5MP, processador Exynos 1380, 8GB RAM, 256GB armazenamento.', 'Smartphone Samsung Galaxy A54 256GB', 1299.99, 1499.99, 1, 1, 'SAMSUNG-A54-256', 25, 'active', 0.5, datetime('now'), datetime('now')),

(2, 'Notebook Dell Inspiron 15 3000', 'notebook-dell-inspiron-15-3000', 'Notebook com processador Intel Core i5, 8GB RAM, SSD 256GB, tela 15.6 polegadas Full HD, Windows 11.', 'Notebook Dell Inspiron 15 i5 8GB 256GB SSD', 2499.99, 2799.99, 1, 1, 'DELL-INSP-15-I5', 15, 'active', 2.5, datetime('now'), datetime('now')),

(3, 'Smart TV LG 55" 4K UHD', 'smart-tv-lg-55-4k-uhd', 'Smart TV LED 55 polegadas, resolução 4K UHD, HDR, ThinQ AI, webOS, Wi-Fi, Bluetooth, 3 HDMI, 2 USB.', 'Smart TV LG 55" 4K UHD ThinQ AI', 2199.99, 2599.99, 1, 1, 'LG-TV-55-4K', 10, 'active', 15.0, datetime('now'), datetime('now')),

(4, 'Fone de Ouvido Sony WH-1000XM4', 'fone-de-ouvido-sony-wh-1000xm4', 'Fone de ouvido wireless com cancelamento de ruído, bateria de 30 horas, Bluetooth 5.0, controle por toque.', 'Fone Sony WH-1000XM4 Noise Cancelling', 899.99, 1199.99, 1, 1, 'SONY-WH1000XM4', 20, 'active', 0.3, datetime('now'), datetime('now')),

(5, 'Tablet Apple iPad Air 64GB', 'tablet-apple-ipad-air-64gb', 'iPad Air com chip A14 Bionic, tela Liquid Retina de 10.9 polegadas, 64GB, Wi-Fi, Touch ID, câmera 12MP.', 'iPad Air 64GB Wi-Fi Tela 10.9"', 3499.99, 3899.99, 1, 1, 'APPLE-IPAD-AIR-64', 8, 'active', 0.6, datetime('now'), datetime('now'));

-- Verificar se os dados foram inseridos
SELECT 'Usuários:', COUNT(*) FROM users;
SELECT 'Seller Profiles:', COUNT(*) FROM seller_profiles;
SELECT 'Categorias:', COUNT(*) FROM categories;
SELECT 'Produtos:', COUNT(*) FROM products;

-- Listar produtos criados
SELECT 'Produtos criados:' as info;
SELECT id, name, price, status FROM products;