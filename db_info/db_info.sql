CREATE TABLE `AccountTakeTaka`.`users` ( 
`user_id` INT NOT NULL AUTO_INCREMENT , 
`email` VARCHAR(255) NOT NULL , 
`password` VARCHAR(32) NOT NULL , 
`user_name` VARCHAR(255) NOT NULL , 
`activation_code` INT NOT NULL , 
`created_at` DATE NOT NULL, 
`updated_at` DATE NOT NULL , 
PRIMARY KEY (`user_id`)
);

CREATE TABLE `AccountTakeTaka`.`registers`(
    `regist_id` INT NOT NULL AUTO_INCREMENT,
    `user_id` INT,
    `date` DATE NOT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `income` INT NULL,
    `spending` INT NULL,
    `created_at` DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id), 
    PRIMARY KEY (`regist_id`)
);



CREATE TABLE `AccountTakeTaka`.`tags` (
    `regist_id` INT,
    `tag_name` VARCHAR(255) NOT NULL,
    `user_id` INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id), 
    FOREIGN KEY (regist_id) REFERENCES registers(regist_id)
);