-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema db_octhum
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema db_octhum
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `db_octhum` DEFAULT CHARACTER SET utf8 ;
USE `db_octhum` ;

-- -----------------------------------------------------
-- Table `db_octhum`.`tbl_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_octhum`.`tbl_user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `created_at` TIMESTAMP NOT NULL,
  `updated_at` TIMESTAMP NULL,
  `id_resp_inc` INT NOT NULL,
  `id_resp_alt` INT NULL,
  `email` VARCHAR(45) NOT NULL,
  `password` VARCHAR(500) NOT NULL,
  `username` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_octhum`.`tbl_intelligence_category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_octhum`.`tbl_intelligence_category` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `category` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_octhum`.`tbl_intelligence_file_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_octhum`.`tbl_intelligence_file_type` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_octhum`.`tbl_intelligence`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_octhum`.`tbl_intelligence` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `id_resp_inc` INT NOT NULL,
  `id_resp_alt` INT NULL,
  `created_at` TIMESTAMP NOT NULL,
  `updated_at` TIMESTAMP NULL,
  `description` VARCHAR(500) NULL,
  `id_category` INT NOT NULL,
  `id_file_type` INT NOT NULL,
  PRIMARY KEY (`id`, `id_category`, `id_file_type`),
  INDEX `fk_tbl_intelligence_tbl_user1_idx` (`id_resp_inc` ASC),
  INDEX `fk_tbl_intelligence_tbl_intelligence_category1_idx` (`id_category` ASC),
  INDEX `fk_tbl_intelligence_tbl_intelligence_file_type1_idx` (`id_file_type` ASC),
  CONSTRAINT `fk_tbl_intelligence_tbl_user1`
    FOREIGN KEY (`id_resp_inc`)
    REFERENCES `db_octhum`.`tbl_user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbl_intelligence_tbl_intelligence_category1`
    FOREIGN KEY (`id_category`)
    REFERENCES `db_octhum`.`tbl_intelligence_category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbl_intelligence_tbl_intelligence_file_type1`
    FOREIGN KEY (`id_file_type`)
    REFERENCES `db_octhum`.`tbl_intelligence_file_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_octhum`.`tbl_mlp`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_octhum`.`tbl_mlp` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_resp_inc` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL,
  `updated_at` INT NULL,
  `id_resp_alt` INT NOT NULL,
  `conf_file_name` VARCHAR(200) NOT NULL,
  `id_intelligence` INT NOT NULL,
  PRIMARY KEY (`id`, `id_intelligence`),
  INDEX `fk_tbl_neural_network_tbl_user1_idx` (`id_resp_alt` ASC),
  INDEX `fk_tbl_mlp_tbl_intelligence1_idx` (`id_intelligence` ASC),
  CONSTRAINT `fk_tbl_neural_network_tbl_user1`
    FOREIGN KEY (`id_resp_inc`)
    REFERENCES `db_octhum`.`tbl_user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbl_mlp_tbl_intelligence1`
    FOREIGN KEY (`id_intelligence`)
    REFERENCES `db_octhum`.`tbl_intelligence` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `db_octhum`.`tbl_log_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_octhum`.`tbl_log_type` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
COMMENT = 'type log\n\ninitialy creation or use';


-- -----------------------------------------------------
-- Table `db_octhum`.`tbl_intelligence_log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_octhum`.`tbl_intelligence_log` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `file_name` VARCHAR(45) NOT NULL,
  `id_intelligence` INT NOT NULL,
  `id_log_type` INT NOT NULL,
  `date` TIMESTAMP NOT NULL,
  `description` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id`, `id_intelligence`, `id_log_type`),
  INDEX `fk_tbl_intelligence_log_tbl_intelligence1_idx` (`id_intelligence` ASC),
  INDEX `fk_tbl_intelligence_log_tbl_type_log1_idx` (`id_log_type` ASC),
  CONSTRAINT `fk_tbl_intelligence_log_tbl_intelligence1`
    FOREIGN KEY (`id_intelligence`)
    REFERENCES `db_octhum`.`tbl_intelligence` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_tbl_intelligence_log_tbl_type_log1`
    FOREIGN KEY (`id_log_type`)
    REFERENCES `db_octhum`.`tbl_log_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'Path to the log file in the file system\n\nlog type is a char that represents the type of the log (creation or use)';


-- -----------------------------------------------------
-- Table `db_octhum`.`tbl_mlp_variable`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_octhum`.`tbl_mlp_variable` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_mlp` INT NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`, `id_mlp`),
  INDEX `fk_tbl_mlp_variables_tbl_mlp1_idx` (`id_mlp` ASC),
  CONSTRAINT `fk_tbl_mlp_variables_tbl_mlp1`
    FOREIGN KEY (`id_mlp`)
    REFERENCES `db_octhum`.`tbl_mlp` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'MLP variables. In color example, are r, g, and b';


-- -----------------------------------------------------
-- Table `db_octhum`.`tbl_mlp_classification`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_octhum`.`tbl_mlp_classification` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `output_number` VARCHAR(45) NOT NULL,
  `id_mlp` INT NOT NULL,
  PRIMARY KEY (`id`, `id_mlp`),
  INDEX `fk_tbl_mlp_classification_tbl_mlp1_idx` (`id_mlp` ASC),
  CONSTRAINT `fk_tbl_mlp_classification_tbl_mlp1`
    FOREIGN KEY (`id_mlp`)
    REFERENCES `db_octhum`.`tbl_mlp` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'classifications of each mlp\n';


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
