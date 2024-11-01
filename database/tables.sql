CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `username` varchar(255) NOT NULL,
    `contact_no` varchar(50) NOT NULL,
    `email_address` varchar(255) NOT NULL,
    `password_hash` varchar(255) NOT NULL,
    `role` enum(
        'Administrator',
        'Inventory Manager',
        'Finance Manager',
        'HR Manager',
        'Staff'
    ) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `contact_no` (`contact_no`),
    UNIQUE KEY `email_address` (`email_address`)
);

CREATE TABLE `material` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `description` varchar(255) NOT NULL,
    `material_type` varchar(255) NOT NULL,
    `expiration_date` date DEFAULT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `purchase` (
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
    `id` int(11) NOT NULL,
    `pm_purchase_id` int(11) NOT NULL,
    `pm_material_id` int(11) NOT NULL,
    `quantity` int(11) NOT NULL,
    `unit_price` double NOT NULL,
    `total_price` double NOT NULL,
    `batch_number` varchar(255) NOT NULL,
    PRIMARY KEY (`id`, `pm_purchase_id`, `pm_material_id`),
    UNIQUE KEY `batch_number` (`batch_number`),
    KEY `pm_purchase_id` (`pm_purchase_id`),
    KEY `pm_material_id` (`pm_material_id`),
    CONSTRAINT `purchase_material_ibfk_1` FOREIGN KEY (`pm_purchase_id`) REFERENCES `purchase` (`id`) ON DELETE NO ACTION,
    CONSTRAINT `purchase_material_ibfk_2` FOREIGN KEY (`pm_material_id`) REFERENCES `material` (`id`) ON DELETE NO ACTION
);

CREATE TABLE `supplier` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `address` varchar(255) NOT NULL,
    `contact_no` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `contact_no` (`contact_no`)
)