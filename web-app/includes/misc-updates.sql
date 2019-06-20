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

ALTER TABLE `data_perpetualmotion`.`emails_dbtable` 
CHANGE COLUMN `subject` `subject` VARCHAR(200) NOT NULL ;

/* Create prizes available table */
CREATE TABLE `data_perpetualmotion`.`prizes_available_dbtable` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `price` DECIMAL(6,2) NOT NULL,
  `visible` INT NOT NULL default 1,
  PRIMARY KEY (`id`))
  CHARACTER SET = utf8 , ENGINE = InnoDB;

/* Populate prizes available */
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Albion', 10, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Albion', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Bobby O\'Brien\'s Gift Certificate', 0, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Bobby O\'Brien\'s', 10, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Bobby O\'Brien\'s', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Bobby O\'Brien\'s', 25, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Borealis', 25, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Boston Pizza', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Boston Pizza', 25, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Buffalo Wild Wings', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Fifty West Bar Gift Certificate', 0, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Fifty West Gift Certificate', 0, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Fionn MacCool\'s', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Frank and Steins Gift Certificate', 0, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Kelsey\'s', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Kelsey\'s/Montana\'s Gift Certificate', 0, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('McCabe\'s Gift Certificate', 0, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('McCabe\'s', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('McCabe\'s', 25, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Montana\'s', 10, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Montana\'s', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Montana\'s', 25, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('N/A', 0, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Shoeless Joe\'s Gift Certificate', 0, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Shoeless Joes', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Sip Club', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Squirrel Tooth Alice\'s', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Stampede Ranch Gift Certificate', 0, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Stampede Ranch', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Western Hotel', 20, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Western Hotel', 25, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Woolwich Arrow', 15, 0);
INSERT INTO prizes_available_dbtable (`name`, `price`, `visible`) VALUES ('Woolwich Arrow', 25, 0);

/* Update prizes table to link to prizes available */
ALTER TABLE `data_perpetualmotion`.`prizes` 
CHARACTER SET = utf8 , ENGINE = InnoDB ,
ADD COLUMN `prize_available_id` INT NULL DEFAULT NULL AFTER `prize_time_frame`,
ADD INDEX `FK_Prize_Prize_Available_idx` (`prize_available_id` ASC);
;
ALTER TABLE `data_perpetualmotion`.`prizes` 
ADD CONSTRAINT `FK_Prize_Prize_Available`
  FOREIGN KEY (`prize_available_id`)
  REFERENCES `data_perpetualmotion`.`prizes_available_dbtable` (`id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;
  
update data_perpetualmotion.prizes
LEFT JOIN prizes_available_dbtable pa1 ON prizes.prize_description = pa1.name
LEFT JOIN prizes_available_dbtable pa2 ON prizes.prize_description = concat(pa2.name, '-', cast(truncate(pa2.price, 0) as char))
set prizes.prize_available_id = coalesce(pa1.id,pa2.id)
where (pa1.id is not null OR pa2.id is not null);

/* Set current prizes visible */
update data_perpetualmotion.prizes_available_dbtable as pa
set pa.visible = 1
where concat(pa.name, '-', cast(truncate(pa.price, 0) as char)) in 
	('Shoeless Joes-20', 'Stampede Ranch-20', 'Buffalo Wild Wings-20', 'Borealis-25', 'Borealis-20', 'Fionn MacCool\'s-20', 'Bobby O\'Brien\'s-20', 'Kelsey\'s-20', 'Western Hotel-20', 'McCabe\'s-20', 'Boston Pizza-20', 'Albion-20', 'Sip Club-20', 'Boston Pizza-25', 'Montana\'s-20', 'Western Hotel-25', 'Bobby O\'Brien\'s-25', 'McCabe\'s-25', 'Western Hotel-25', 'Albion-25');
	
/* Create prize winner timeframes */
CREATE TABLE `data_perpetualmotion`.`prizes_timeframes_dbtable` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `description` VARCHAR(100) NOT NULL,
  `visible` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
  CHARACTER SET = utf8 , ENGINE = InnoDB;

INSERT INTO `prizes_timeframes_dbtable` (`id`, `description`, `visible`) VALUES (3, 'Spring 2014', 1);
INSERT INTO `prizes_timeframes_dbtable` (`id`, `description`, `visible`) VALUES (4, 'Summer 2014', 1);
INSERT INTO `prizes_timeframes_dbtable` (`id`, `description`, `visible`) VALUES (5, 'Spring 2015', 1);
INSERT INTO `prizes_timeframes_dbtable` (`id`, `description`, `visible`) VALUES (6, 'Spring 20167-Spring 2017', 1);
INSERT INTO `prizes_timeframes_dbtable` (`id`, `description`, `visible`) VALUES (7, 'Spring 2017', 1);
INSERT INTO `prizes_timeframes_dbtable` (`id`, `description`, `visible`) VALUES (8, 'Spring 2018', 1);
INSERT INTO `prizes_timeframes_dbtable` (`id`, `description`, `visible`) VALUES (9, 'Spring 2019', 1);

/* Join prizes table with prize timeframes table */
ALTER TABLE `data_perpetualmotion`.`prizes` 
CHANGE COLUMN `prize_time_frame` `prize_time_frame` INT(11) NULL DEFAULT NULL ;

update data_perpetualmotion.prizes
left join prizes_timeframes_dbtable prizetime ON prizetime.id = prizes.prize_time_frame
set prizes.prize_time_frame = null
where prizetime.id is null;

ALTER TABLE `data_perpetualmotion`.`prizes` 
ADD INDEX `FK_Prize_Timeframe_idx` (`prize_time_frame` ASC);
;
ALTER TABLE `data_perpetualmotion`.`prizes` 
ADD CONSTRAINT `FK_Prize_Timeframe`
  FOREIGN KEY (`prize_time_frame`)
  REFERENCES `data_perpetualmotion`.`prizes_timeframes_dbtable` (`id`)
  ON DELETE SET NULL
  ON UPDATE NO ACTION;
