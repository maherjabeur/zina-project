-- Execute this file only once if you cannot run Doctrine migrations on Ooredoo.
-- If an index already exists, phpMyAdmin/MySQL can report a duplicate index error.

CREATE INDEX idx_product_active_created ON product (is_active, created_at);
CREATE INDEX idx_product_category_active ON product (category_id, is_active);
CREATE INDEX idx_promotion_product_active_discount ON promotion (product_id, is_active, discount);
CREATE INDEX idx_promotion_dates ON promotion (start_date, end_date);
CREATE INDEX idx_category_active_position ON category (is_active, position);
CREATE INDEX idx_size_active_position ON size (is_active, position);
CREATE INDEX idx_size_code_active ON size (code, is_active);
CREATE INDEX idx_product_image_product_position ON product_image (product_id, position);
CREATE INDEX idx_order_status_created ON `order` (status, created_at);
CREATE INDEX idx_order_number ON `order` (order_number);
