-- Sales Tables for POS System
-- This file contains only the sales-related tables needed for Sales History and Sales Report functionality

-- Sales Sessions (Cash Registers)
CREATE TABLE sales_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    session_end TIMESTAMP NULL,
    opening_amount DECIMAL(10,2) DEFAULT 0.00,
    closing_amount DECIMAL(10,2) DEFAULT 0.00,
    total_sales DECIMAL(10,2) DEFAULT 0.00,
    total_refunds DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('open', 'closed') DEFAULT 'open',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Sales (Main Sales Table)
CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    session_id INT,
    member_id INT NULL,
    user_id INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    tax_amount DECIMAL(10,2) DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'ewallet') DEFAULT 'cash',
    payment_status ENUM('paid', 'pending', 'refunded') DEFAULT 'paid',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sales_sessions(id),
    FOREIGN KEY (member_id) REFERENCES members(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Sale Items (Individual items in each sale)
CREATE TABLE sale_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Stock Movements (Track inventory changes from sales)
CREATE TABLE stock_movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    movement_type ENUM('in', 'out', 'adjustment') NOT NULL,
    quantity INT NOT NULL,
    reference_type ENUM('sale', 'purchase', 'stock_take', 'adjustment') NOT NULL,
    reference_id INT,
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Sample data for testing (optional - remove if not needed)
-- Insert sample sales session
INSERT INTO sales_sessions (user_id, opening_amount, total_sales, status) VALUES 
(1, 100.00, 0.00, 'open');

-- Insert sample sales
INSERT INTO sales (invoice_number, session_id, member_id, user_id, subtotal, total_amount, payment_method, payment_status) VALUES 
('INV-2024-001', 1, 1, 1, 150.00, 150.00, 'cash', 'paid'),
('INV-2024-002', 1, NULL, 1, 75.50, 75.50, 'card', 'paid'),
('INV-2024-003', 1, 2, 1, 200.00, 180.00, 'ewallet', 'paid');

-- Insert sample sale items
INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, total_price) VALUES 
(1, 1, 2, 50.00, 100.00),
(1, 2, 1, 50.00, 50.00),
(2, 3, 1, 75.50, 75.50),
(3, 1, 3, 50.00, 150.00),
(3, 4, 1, 30.00, 30.00);

-- Insert sample stock movements
INSERT INTO stock_movements (product_id, movement_type, quantity, reference_type, reference_id, created_by) VALUES 
(1, 'out', 2, 'sale', 1, 1),
(2, 'out', 1, 'sale', 1, 1),
(3, 'out', 1, 'sale', 2, 1),
(1, 'out', 3, 'sale', 3, 1),
(4, 'out', 1, 'sale', 3, 1);


