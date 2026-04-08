DROP DATABASE IF EXISTS pharmasync;
CREATE DATABASE pharmasync CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pharmasync;

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    contact_no VARCHAR(50) NOT NULL,
    gender VARCHAR(50) NOT NULL,
    username VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    profile_picture LONGBLOB NULL,
    role ENUM('administrator', 'inventory_manager', 'finance_manager', 'hr_manager', 'staff') NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    UNIQUE KEY uq_users_contact_no (contact_no),
    UNIQUE KEY uq_users_email_address (email_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE activity_log (
    id INT NOT NULL AUTO_INCREMENT,
    action VARCHAR(100) NOT NULL,
    user_id INT NULL,
    description TEXT NOT NULL,
    timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_activity_user_id (user_id),
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE suppliers (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    contact_no VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_suppliers_email (email),
    UNIQUE KEY uq_suppliers_contact_no (contact_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE customers (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    contact_no VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_customers_email (email),
    UNIQUE KEY uq_customers_contact_no (contact_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE racks (
    id INT NOT NULL AUTO_INCREMENT,
    location VARCHAR(255) NOT NULL,
    temperature_controlled TINYINT NOT NULL,
    capacity DOUBLE NOT NULL DEFAULT 0,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE materials (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    material_type VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE medicines (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    composition VARCHAR(255) NOT NULL,
    therapeutic_class VARCHAR(255) NOT NULL,
    regulatory_class VARCHAR(255) NOT NULL,
    manufacturing_details VARCHAR(255) NOT NULL,
    unit_price DOUBLE NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE lots (
    id INT NOT NULL AUTO_INCREMENT,
    number VARCHAR(255) NOT NULL,
    production_date DATE NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE batches (
    id INT NOT NULL AUTO_INCREMENT,
    production_date DATE NOT NULL,
    rack_id INT NOT NULL,
    PRIMARY KEY (id),
    KEY idx_batches_rack_id (rack_id),
    CONSTRAINT fk_batches_rack FOREIGN KEY (rack_id) REFERENCES racks (id) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE purchases (
    id INT NOT NULL AUTO_INCREMENT,
    date DATE NOT NULL,
    material_count INT NOT NULL,
    total_cost DOUBLE NOT NULL,
    status VARCHAR(50) NOT NULL,
    p_supplier_id INT NOT NULL,
    PRIMARY KEY (id),
    KEY idx_purchases_supplier_id (p_supplier_id),
    CONSTRAINT fk_purchases_supplier FOREIGN KEY (p_supplier_id) REFERENCES suppliers (id) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
    id INT NOT NULL AUTO_INCREMENT,
    date DATE NOT NULL,
    product_count INT NOT NULL,
    total_cost DOUBLE NOT NULL,
    payment_status VARCHAR(255) NOT NULL,
    order_status VARCHAR(255) NOT NULL,
    customer_id INT NOT NULL,
    PRIMARY KEY (id),
    KEY idx_orders_customer_id (customer_id),
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE material_lot (
    lot_id INT NOT NULL,
    material_id INT NOT NULL,
    stock_level INT NOT NULL,
    qc_status VARCHAR(50) NOT NULL,
    qc_notes VARCHAR(255) NOT NULL,
    inspection_date DATE NOT NULL,
    expiration_date DATE NOT NULL,
    PRIMARY KEY (lot_id, material_id),
    KEY idx_material_lot_material_id (material_id),
    CONSTRAINT fk_material_lot_lot FOREIGN KEY (lot_id) REFERENCES lots (id) ON DELETE NO ACTION,
    CONSTRAINT fk_material_lot_material FOREIGN KEY (material_id) REFERENCES materials (id) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE purchase_material (
    pm_purchase_id INT NOT NULL,
    pm_material_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DOUBLE NOT NULL,
    total_price DOUBLE NOT NULL,
    lot_id INT NOT NULL,
    PRIMARY KEY (pm_purchase_id, pm_material_id),
    KEY idx_purchase_material_material_id (pm_material_id),
    KEY idx_purchase_material_lot_id (lot_id),
    CONSTRAINT fk_purchase_material_purchase FOREIGN KEY (pm_purchase_id) REFERENCES purchases (id) ON DELETE NO ACTION,
    CONSTRAINT fk_purchase_material_material FOREIGN KEY (pm_material_id) REFERENCES materials (id) ON DELETE NO ACTION,
    CONSTRAINT fk_purchase_material_lot FOREIGN KEY (lot_id) REFERENCES lots (id) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE medicine_batch (
    medicine_id INT NOT NULL,
    batch_id INT NOT NULL,
    stock_level INT NOT NULL,
    expiry_date DATE NOT NULL,
    PRIMARY KEY (medicine_id, batch_id),
    KEY idx_medicine_batch_batch_id (batch_id),
    CONSTRAINT fk_medicine_batch_medicine FOREIGN KEY (medicine_id) REFERENCES medicines (id) ON DELETE NO ACTION,
    CONSTRAINT fk_medicine_batch_batch FOREIGN KEY (batch_id) REFERENCES batches (id) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_medicine (
    order_id INT NOT NULL,
    medicine_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DOUBLE NOT NULL,
    total_price DOUBLE NOT NULL,
    batch_id INT NOT NULL,
    PRIMARY KEY (order_id, medicine_id, batch_id),
    KEY idx_order_medicine_medicine_id (medicine_id),
    KEY idx_order_medicine_batch_id (batch_id),
    CONSTRAINT fk_order_medicine_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE NO ACTION,
    CONSTRAINT fk_order_medicine_medicine FOREIGN KEY (medicine_id) REFERENCES medicines (id) ON DELETE NO ACTION,
    CONSTRAINT fk_order_medicine_batch FOREIGN KEY (batch_id) REFERENCES batches (id) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_formulation (
    id INT NOT NULL AUTO_INCREMENT,
    medicine_id INT NOT NULL,
    material_id INT NOT NULL,
    quantity_required DOUBLE NOT NULL,
    unit VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    PRIMARY KEY (id),
    KEY idx_product_formulation_medicine_id (medicine_id),
    KEY idx_product_formulation_material_id (material_id),
    CONSTRAINT fk_product_formulation_medicine FOREIGN KEY (medicine_id) REFERENCES medicines (id) ON DELETE NO ACTION,
    CONSTRAINT fk_product_formulation_material FOREIGN KEY (material_id) REFERENCES materials (id) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO users
(first_name, last_name, email_address, contact_no, gender, username, password_hash, profile_picture, role)
VALUES
('System', 'Admin', 'admin@pharmasync.local', '09170000001', 'male', 'admin', '$2y$10$6ZLPEldlL8kb7c/5hHunNu9PbhdULjRJF81IIPYlgWKdwqCfN6vH.', NULL, 'administrator'),
('Ivy', 'Reyes', 'inventory@pharmasync.local', '09170000002', 'female', 'inventory_mgr', '$2y$10$6ZLPEldlL8kb7c/5hHunNu9PbhdULjRJF81IIPYlgWKdwqCfN6vH.', NULL, 'inventory_manager'),
('Finn', 'Cruz', 'finance@pharmasync.local', '09170000003', 'male', 'finance_mgr', '$2y$10$6ZLPEldlL8kb7c/5hHunNu9PbhdULjRJF81IIPYlgWKdwqCfN6vH.', NULL, 'finance_manager');

INSERT INTO suppliers (name, email, address, contact_no)
VALUES
('BioRaw Chemicals Inc.', 'sales@bioraw.test', 'Makati, Metro Manila', '09181111111'),
('Prime Excipients Co.', 'contact@primeexcipient.test', 'Cebu City, Cebu', '09182222222');

INSERT INTO customers (name, email, address, contact_no)
VALUES
('Mercury Test Branch', 'branch@mercury.test', 'Quezon City', '09183333333'),
('Health Plus Pharmacy', 'orders@healthplus.test', 'Davao City', '09184444444');

INSERT INTO racks (location, temperature_controlled, capacity)
VALUES
('A1 - Dry Storage', 0, 1000),
('C2 - Cold Storage', 1, 800);

INSERT INTO materials (name, description, material_type)
VALUES
('Paracetamol API', 'Active ingredient for pain relief tablets', 'raw'),
('Microcrystalline Cellulose', 'Tablet binder and filler', 'excipient'),
('Dextromethorphan HBr', 'Cough suppressant active ingredient', 'raw');

INSERT INTO medicines
(name, type, composition, therapeutic_class, regulatory_class, manufacturing_details, unit_price)
VALUES
('Paracetamol 500mg', 'Tablet', 'Paracetamol 500mg', 'Analgesic', 'OTC', 'Batch manufactured under GMP line 1', 180.00),
('Cough Relief Syrup', 'Syrup', 'Dextromethorphan HBr + excipients', 'Antitussive', 'OTC', 'Liquid line, amber bottle', 300.00);

INSERT INTO lots (number, production_date)
VALUES
('LOT-RM-2026-001', '2026-03-20'),
('LOT-RM-2026-002', '2026-03-25');

INSERT INTO batches (production_date, rack_id)
VALUES
('2026-03-28', 1),
('2026-04-01', 2);

INSERT INTO material_lot
(lot_id, material_id, stock_level, qc_status, qc_notes, inspection_date, expiration_date)
VALUES
(1, 1, 100, 'approved', 'Purity and moisture within limits', '2026-03-21', '2028-03-20'),
(1, 2, 50, 'approved', 'Visual and particle checks passed', '2026-03-21', '2029-03-20'),
(2, 3, 80, 'approved', 'Assay result compliant', '2026-03-26', '2028-03-25');

INSERT INTO purchases (date, material_count, total_cost, status, p_supplier_id)
VALUES
('2026-03-22', 2, 24500.00, 'completed', 1),
('2026-03-27', 1, 7200.00, 'completed', 2);

INSERT INTO purchase_material
(pm_purchase_id, pm_material_id, quantity, unit_price, total_price, lot_id)
VALUES
(1, 1, 100, 120.00, 12000.00, 1),
(1, 2, 50, 250.00, 12500.00, 1),
(2, 3, 80, 90.00, 7200.00, 2);

INSERT INTO medicine_batch (medicine_id, batch_id, stock_level, expiry_date)
VALUES
(1, 1, 500, '2028-03-28'),
(2, 2, 300, '2027-10-01');

INSERT INTO orders (date, product_count, total_cost, payment_status, order_status, customer_id)
VALUES
('2026-04-03', 2, 3900.00, 'paid', 'completed', 1),
('2026-04-05', 1, 1800.00, 'pending', 'processing', 2);

INSERT INTO order_medicine
(order_id, medicine_id, quantity, unit_price, total_price, batch_id)
VALUES
(1, 1, 10, 180.00, 1800.00, 1),
(1, 2, 7, 300.00, 2100.00, 2),
(2, 1, 10, 180.00, 1800.00, 1);

INSERT INTO product_formulation
(medicine_id, material_id, quantity_required, unit, description)
VALUES
(1, 1, 500, 'mg', 'Active ingredient for each tablet'),
(1, 2, 150, 'mg', 'Binder/filler for tablet stability'),
(2, 3, 250, 'mg', 'Active ingredient for each 10ml syrup dose');

INSERT INTO activity_log (action, user_id, description)
VALUES
('seed', 1, 'Database seeded with initial sample records.'),
('create_purchase', 2, 'Added sample purchase transactions.'),
('create_order', 1, 'Added sample customer orders.');
