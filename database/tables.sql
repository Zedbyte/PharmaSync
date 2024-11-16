CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `first_name` varchar(255) NOT NULL,
    `last_name` varchar(255) NOT NULL,
    `email_address` varchar(255) NOT NULL,
    `contact_no` varchar(50) NOT NULL,
    `gender` varchar(50) NOT NULL,
    `username` varchar(255) NOT NULL,
    `password_hash` varchar(255) NOT NULL,
    `role` enum(
        'administrator',
        'inventory_manager',
        'finance_manager',
        'hr_manager',
        'staff'
    ) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `contact_no` (`contact_no`),
    UNIQUE KEY `email_address` (`email_address`)
);

CREATE TABLE `materials` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` varchar(255) NOT NULL,
    `material_type` varchar(255) NOT NULL,
    `expiration_date` date DEFAULT NULL,
    `qc_status` varchar(50) NOT NULL,
    `inspection_date` date DEFAULT NULL,
    `qc_notes` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `purchases` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` date NOT NULL,
    `material_count` int(11) NOT NULL,
    `total_cost` double NOT NULL,
    `status` varchar(50) NOT NULL,
    `p_supplier_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `supplier_id` (`p_supplier_id`),
    CONSTRAINT `purchase_ibfk_1` FOREIGN KEY (`p_supplier_id`) REFERENCES `supplier` (`id`) ON DELETE NO ACTION
); 

CREATE TABLE `purchase_material` (
    `pm_purchase_id` int(11) NOT NULL,
    `pm_material_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL,
    `unit_price` double NOT NULL,
    `total_price` double NOT NULL,
    `batch_number` varchar(255) NOT NULL,
    PRIMARY KEY (`pm_purchase_id`, `pm_material_id`),
    KEY `pm_material_id` (`pm_material_id`),
    CONSTRAINT `purchase_material_ibfk_1` FOREIGN KEY (`pm_material_id`) REFERENCES `materials` (`id`) ON DELETE NO ACTION,
    CONSTRAINT `purchase_material_ibfk_2` FOREIGN KEY (`pm_purchase_id`) REFERENCES `purchases` (`id`) ON DELETE NO ACTION
);

CREATE TABLE `suppliers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `address` varchar(255) NOT NULL,
    `contact_no` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `contact_no` (`contact_no`)
)

CREATE TABLE `orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` date NOT NULL,
    `product_count` int(11) NOT NULL,
    `total_cost` double NOT NULL,
    `payment_status` varchar(255) NOT NULL,
    `order_status` varchar(255) NOT NULL,
    `customer_id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) 

CREATE TABLE `medicines` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
    `composition` varchar(255) NOT NULL,
    `therapeutic_class` varchar(255) NOT NULL,
    `regulatory_class` varchar(255) NOT NULL,
    `manufacturing_details` varchar(255) NOT NULL,
    `formulation_id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) 

CREATE TABLE `order_medicine` (
    `order_id` int(11) NOT NULL,
    `purchase_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL,
    `unit_price` double NOT NULL,
    `total_price` double NOT NULL,
    PRIMARY KEY (`order_id`, `purchase_id`)
)