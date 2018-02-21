/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  derek
 * Created: 10-Feb-2018
 */

--Create Emails Table
CREATE TABLE `data_perpetualmotion`.`emails_dbtable` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email_type` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(100) NOT NULL,
  `content` TEXT NOT NULL,
  `to_email` VARCHAR(300) NULL DEFAULT NULL,
  `from_name` VARCHAR(100) NOT NULL,
  `from_email` VARCHAR(100) NOT NULL,
  `cc_email` VARCHAR(300) NULL DEFAULT NULL,
  `bcc_email` VARCHAR(300) NULL DEFAULT NULL,
  `created_date` DATETIME NOT NULL,
  `sent_date` DATETIME NULL DEFAULT NULL,
  `error_msg` TEXT NULL DEFAULT NULL,
  PRIMARY KEY (`id`));


CREATE TABLE `data_perpetualmotion`.`properties_dbtable` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(100) NOT NULL,
  `value` VARCHAR(45) NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `data_perpetualmotion`.`properties_dbtable` 
ADD UNIQUE INDEX `UNIQUE_PROPERTY_KEY` (`key` ASC);

ALTER TABLE `data_perpetualmotion`.`properties_dbtable` 
ADD INDEX `INDEX_PROPERTY_KEY` (`key` ASC);
