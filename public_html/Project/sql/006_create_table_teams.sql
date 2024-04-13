CREATE TABLE IF NOT EXISTS `NBA_Teams` (
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `nickname` VARCHAR(100) NOT NULL,
    `city` VARCHAR(100) NOT NULL,
    `logo` VARCHAR(255) NOT NULL,
    `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE(`name`)
);
