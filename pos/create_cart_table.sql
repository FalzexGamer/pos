-- Create simplified cart table
USE pos_system;

-- Drop existing cart tables if they exist
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS cart_sessions;

-- Create new simplified cart table
CREATE TABLE cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    sku VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    status ENUM('active','ordered','abandoned') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Create indexes for better performance
CREATE INDEX idx_cart_user_status ON cart(user_id, status);
CREATE INDEX idx_cart_product ON cart(product_id);
CREATE INDEX idx_cart_sku ON cart(sku);
CREATE INDEX idx_cart_created ON cart(created_at);

-- Insert sample data (optional)
-- INSERT INTO cart (user_id, product_id, sku, quantity, price, subtotal) VALUES (1, 1, 'PROD001', 2, 3000.00, 6000.00);
