-- Faith & Grace Christian Store — database setup
-- Import this file in phpMyAdmin (Import tab) or run: mysql -u root < database.sql

CREATE DATABASE IF NOT EXISTS faith_grace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE faith_grace;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  category VARCHAR(80) NOT NULL,
  description TEXT NULL,
  price DECIMAL(10,2) NOT NULL,
  icon VARCHAR(20) NOT NULL DEFAULT '✝',
  image VARCHAR(255) NULL,
  stock INT NOT NULL DEFAULT 50,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_category (category),
  INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(150) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  email VARCHAR(150) NULL,
  address VARCHAR(255) NOT NULL,
  notes VARCHAR(500) NULL,
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
  delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 0,
  total DECIMAL(10,2) NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'Pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_status (status),
  INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NULL,
  product_name VARCHAR(150) NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS subscribers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample products
INSERT INTO products (name, category, description, price, icon, image, stock) VALUES
('Study Bible', 'Bibles & Books', 'A clear, readable study edition with notes and cross-references for daily reading.', 180.00, '📖', 'assets/img/products/study-bible.svg', 40),
('Daily Devotional', 'Bibles & Books', 'A year of short morning readings and prayers to anchor your day in Scripture.', 95.00, '📚', 'assets/img/products/daily-devotional.svg', 60),
('Faith T-Shirt', 'Clothing', 'Soft, breathable cotton tee with a subtle faith-inspired design.', 120.00, '👕', 'assets/img/products/faith-tshirt.svg', 80),
('Grace Hoodie', 'Clothing', 'A warm, midweight hoodie made for cool mornings and quiet moments.', 240.00, '🧥', 'assets/img/products/grace-hoodie.svg', 35),
('Cross Necklace', 'Accessories', 'A simple polished cross pendant on a fine chain. A timeless everyday reminder.', 85.00, '✝️', 'assets/img/products/cross-necklace.svg', 70),
('Prayer Bracelet', 'Accessories', 'Handcrafted prayer bead bracelet to carry with you through the day.', 55.00, '📿', 'assets/img/products/prayer-bracelet.svg', 90),
('Faith Mug', 'Gifts', 'A ceramic mug for your morning coffee or tea, printed with an encouraging verse.', 65.00, '☕', 'assets/img/products/faith-mug.svg', 100),
('Blessed Gift Box', 'Gifts', 'A thoughtfully assembled gift box, ready to bless a friend or loved one.', 150.00, '🎁', 'assets/img/products/blessed-gift-box.svg', 25);

-- Default admin login:  username: admin   password: Grace@2026
-- IMPORTANT: change this password right after your first login (Admin → Account).
INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$10$R.7ay6X.DW1xdD23qosEXOE.bZKnNX4.PjPZQXUxpYCX81l7r4Beq');
