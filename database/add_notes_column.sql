-- Add notes column to cart table
USE pos;
ALTER TABLE cart ADD COLUMN notes TEXT AFTER subtotal;
