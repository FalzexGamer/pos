-- Add notes column to sale_items table
USE pos;
ALTER TABLE sale_items ADD COLUMN notes TEXT AFTER total_price;
