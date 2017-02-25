SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA IF NOT EXISTS `SCMS` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `SCMS` ;

-- -----------------------------------------------------
-- Table `SCMS`.`Languages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`Languages` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`Languages` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(45) NULL ,
  `Direction` VARCHAR(3) NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`Users` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`Users` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `Email` VARCHAR(60) NULL ,
  `Password` VARCHAR(100) NULL ,
  `Registration` DATETIME NULL ,
  `LastLogin` DATETIME NULL ,
  `SSRFEmail` VARCHAR(60) NULL ,
  `Languages_id` INT NOT NULL ,
  `AuthenticationToken` VARCHAR(100) NULL ,
  `Admin` VARCHAR(45) NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `fk_Users_Languages1_idx` (`Languages_id` ASC) ,
  CONSTRAINT `fk_Users_Languages1`
    FOREIGN KEY (`Languages_id` )
    REFERENCES `SCMS`.`Languages` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`UserGroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`UserGroups` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`UserGroups` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(45) NOT NULL ,
  `Created` DATETIME NOT NULL ,
  `AssignLoggedOutUsers` BIT NOT NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`UsersInGroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`UsersInGroups` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`UsersInGroups` (
  `Users_Id` INT NOT NULL AUTO_INCREMENT ,
  `UserGroups_Id` INT NOT NULL ,
  INDEX `fk_UsersInGroups_Users_idx` (`Users_Id` ASC) ,
  INDEX `fk_UsersInGroups_usergroups1_idx` (`UserGroups_Id` ASC) ,
  CONSTRAINT `fk_UsersInGroups_Users`
    FOREIGN KEY (`Users_Id` )
    REFERENCES `SCMS`.`Users` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UsersInGroups_usergroups1`
    FOREIGN KEY (`UserGroups_Id` )
    REFERENCES `SCMS`.`UserGroups` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`ContentTypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`ContentTypes` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`ContentTypes` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(45) NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`Applications`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`Applications` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`Applications` (
  `AppId` INT NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(80) NULL ,
  `Key` VARCHAR(80) NULL ,
  PRIMARY KEY (`AppId`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`Content`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`Content` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`Content` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `Parent_Id` INT NULL ,
  `URL` VARCHAR(100) NULL ,
  `ContentTypes_Id` INT NOT NULL ,
  `Author_Id` INT NOT NULL ,
  `Applications_AppId` INT NOT NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `fk_Content_ContentTypes1_idx` (`ContentTypes_Id` ASC) ,
  INDEX `fk_Content_Users1_idx` (`Author_Id` ASC) ,
  INDEX `fk_Content_Applications1_idx` (`Applications_AppId` ASC) ,
  CONSTRAINT `fk_Content_ContentTypes1`
    FOREIGN KEY (`ContentTypes_Id` )
    REFERENCES `SCMS`.`ContentTypes` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Content_Users1`
    FOREIGN KEY (`Author_Id` )
    REFERENCES `SCMS`.`Users` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Content_Applications1`
    FOREIGN KEY (`Applications_AppId` )
    REFERENCES `SCMS`.`Applications` (`AppId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`ContentTypeFields`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`ContentTypeFields` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`ContentTypeFields` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `ContentTypes_Id` INT NOT NULL ,
  `Name` VARCHAR(45) NULL ,
  `Type` VARCHAR(45) NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `fk_ContentTypeFields_ContentTypes1_idx` (`ContentTypes_Id` ASC) ,
  CONSTRAINT `fk_ContentTypeFields_ContentTypes1`
    FOREIGN KEY (`ContentTypes_Id` )
    REFERENCES `SCMS`.`ContentTypes` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`ContentData`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`ContentData` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`ContentData` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `Content_Id` INT NOT NULL ,
  `ContentTypeFields_Id` INT NOT NULL ,
  `Languages_Id` INT NOT NULL ,
  `Value` LONGTEXT NULL ,
  PRIMARY KEY (`Id`, `Languages_Id`, `Content_Id`, `ContentTypeFields_Id`) ,
  INDEX `fk_ContentData_ContentTypeFields1_idx` (`ContentTypeFields_Id` ASC) ,
  INDEX `fk_ContentData_Content1_idx` (`Content_Id` ASC) ,
  INDEX `fk_ContentData_Languages1_idx` (`Languages_Id` ASC) ,
  CONSTRAINT `fk_ContentData_ContentTypeFields1`
    FOREIGN KEY (`ContentTypeFields_Id` )
    REFERENCES `SCMS`.`ContentTypeFields` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ContentData_Content1`
    FOREIGN KEY (`Content_Id` )
    REFERENCES `SCMS`.`Content` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ContentData_Languages1`
    FOREIGN KEY (`Languages_Id` )
    REFERENCES `SCMS`.`Languages` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`ContentAccessRights`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`ContentAccessRights` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`ContentAccessRights` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `UserGroups_id` INT NOT NULL ,
  `Value` VARCHAR(4) NULL ,
  `ContentTypes_id` INT NOT NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `fk_ContentAccessRights_UserGroups1_idx` (`UserGroups_id` ASC) ,
  INDEX `fk_ContentAccessRights_ContentTypes1_idx` (`ContentTypes_id` ASC) ,
  CONSTRAINT `fk_ContentAccessRights_UserGroups1`
    FOREIGN KEY (`UserGroups_id` )
    REFERENCES `SCMS`.`UserGroups` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ContentAccessRights_ContentTypes1`
    FOREIGN KEY (`ContentTypes_id` )
    REFERENCES `SCMS`.`ContentTypes` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`UserDataTypes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`UserDataTypes` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`UserDataTypes` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(45) NULL ,
  `Type` VARCHAR(45) NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`UserData`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`UserData` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`UserData` (
  `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `UserDataTypes_id` INT NOT NULL ,
  `Users_id` INT NOT NULL ,
  `Value` TEXT NULL ,
  PRIMARY KEY (`Id`) ,
  UNIQUE INDEX `id_UNIQUE` (`Id` ASC) ,
  INDEX `fk_UserData_UserDataTypes1_idx` (`UserDataTypes_id` ASC) ,
  INDEX `fk_UserData_Users1_idx` (`Users_id` ASC) ,
  CONSTRAINT `fk_UserData_UserDataTypes1`
    FOREIGN KEY (`UserDataTypes_id` )
    REFERENCES `SCMS`.`UserDataTypes` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UserData_Users1`
    FOREIGN KEY (`Users_id` )
    REFERENCES `SCMS`.`Users` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`ContentDataAccessRights`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`ContentDataAccessRights` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`ContentDataAccessRights` (
  `Id` INT NOT NULL AUTO_INCREMENT ,
  `ContentTypeFields_id` INT NOT NULL ,
  `UserGroups_id` INT NOT NULL ,
  `Value` VARCHAR(4) NULL ,
  PRIMARY KEY (`Id`, `UserGroups_id`, `ContentTypeFields_id`) ,
  INDEX `fk_ContentDataAccessRights_UserGroups1_idx` (`UserGroups_id` ASC) ,
  INDEX `fk_ContentDataAccessRights_ContentTypeFields1_idx` (`ContentTypeFields_id` ASC) ,
  CONSTRAINT `fk_ContentDataAccessRights_UserGroups1`
    FOREIGN KEY (`UserGroups_id` )
    REFERENCES `SCMS`.`UserGroups` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ContentDataAccessRights_ContentTypeFields1`
    FOREIGN KEY (`ContentTypeFields_id` )
    REFERENCES `SCMS`.`ContentTypeFields` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `SCMS`.`AuthKeys`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `SCMS`.`AuthKeys` ;

CREATE  TABLE IF NOT EXISTS `SCMS`.`AuthKeys` (
  `Users_Id` INT NOT NULL ,
  `Applications_AppId` INT NOT NULL ,
  `Key` VARCHAR(80) NULL ,
  `Expire Date` BIGINT NULL ,
  INDEX `fk_AuthKeys_Users1_idx` (`Users_Id` ASC) ,
  INDEX `fk_AuthKeys_Applications1_idx` (`Applications_AppId` ASC) ,
  PRIMARY KEY (`Users_Id`, `Applications_AppId`) ,
  CONSTRAINT `fk_AuthKeys_Users1`
    FOREIGN KEY (`Users_Id` )
    REFERENCES `SCMS`.`Users` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AuthKeys_Applications1`
    FOREIGN KEY (`Applications_AppId` )
    REFERENCES `SCMS`.`Applications` (`AppId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `SCMS` ;

-- -----------------------------------------------------
-- Placeholder table for view `SCMS`.`View_Content`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SCMS`.`View_Content` (`Id` INT, `User_Id` INT, `UserGroups_Id` INT, `UserGroups_Name` INT, `AccessRights` INT, `URL` INT, `Type_Id` INT, `Type_Name` INT, `Author_Id` INT, `Author_Email` INT);

-- -----------------------------------------------------
-- Placeholder table for view `SCMS`.`View_ContentData`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SCMS`.`View_ContentData` (`Id` INT, `Name` INT, `Value` INT, `ContentTypeFields_Id` INT, `Type` INT, `UserGroup` INT, `Access` INT);

-- -----------------------------------------------------
-- Placeholder table for view `SCMS`.`View_Users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SCMS`.`View_Users` (`Id` INT, `Email` INT, `Password` INT, `Registration` INT, `LastLogin` INT, `SSRFEmail` INT, `LanguageName` INT, `LanguageDirection` INT);

-- -----------------------------------------------------
-- Placeholder table for view `SCMS`.`View_UserData`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SCMS`.`View_UserData` (`UserId` INT, `Name` INT, `Type` INT, `Value` INT);

-- -----------------------------------------------------
-- Placeholder table for view `SCMS`.`View_UsersInGroups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SCMS`.`View_UsersInGroups` (`UserId` INT, `email` INT, `password` INT, `AuthenticationToken` INT, `Id` INT, `Name` INT, `Created` INT, `AssignToLoggedOutUsers` INT);

-- -----------------------------------------------------
-- Placeholder table for view `SCMS`.`LoggedOutGroups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SCMS`.`LoggedOutGroups` (`id` INT, `name` INT, `created` INT);

-- -----------------------------------------------------
-- Placeholder table for view `SCMS`.`LoggedOutContentAccessRights`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SCMS`.`LoggedOutContentAccessRights` (`UserGroupName` INT, `Access` INT, `UserGroups_id` INT, `ContentTypes_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `SCMS`.`LoggedOutContentDataAccessRights`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SCMS`.`LoggedOutContentDataAccessRights` (`UserGroupName` INT, `Access` INT, `UserGroups_id` INT, `ContentTypesFields_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `SCMS`.`view_UsersGroups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `SCMS`.`view_UsersGroups` (`Name` INT, `Created` INT, `AssignLoggedOutUsers` INT, `UsersId` INT);

-- -----------------------------------------------------
-- View `SCMS`.`View_Content`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `SCMS`.`View_Content` ;
DROP TABLE IF EXISTS `SCMS`.`View_Content`;
USE `SCMS`;
CREATE  OR REPLACE VIEW `SCMS`.`View_Content` AS
SELECT
`Content`.`Id` as `Id`,
`UsersInGroups`.`Users_id` as `User_Id`,
`ContentAccessRights`.`UserGroups_Id` as `UserGroups_Id`,
`UserGroups`.`name` as `UserGroups_Name`,
`ContentAccessRights`.`value` as `AccessRights`,
`Content`.`URL` as `URL`,
`Content`.`ContentTypes_Id` as `Type_Id`,
`ContentTypes`.`name` as `Type_Name`,
`Content`.`Author_id` as `Author_Id`,
`Author`.`email` as `Author_Email`
FROM `Content`
LEFT JOIN `Applications` on (`Content`.`Applications_AppId` = `Applications`.`AppId`)
LEFT JOIN `AuthKeys` on (`Content`.`Applications_AppId` = `AuthKeys`.`Applications_AppId`)
LEFT JOIN `ContentTypes` on (`Content`.`ContentTypes_Id` = `ContentTypes`.`Id`)
LEFT JOIN `ContentAccessRights` on (`Content`.`ContentTypes_Id` = `ContentAccessRights`.`ContentTypes_Id`)
LEFT JOIN `UsersInGroups` on (`ContentAccessRights`.`UserGroups_Id` = `UsersInGroups`.`UserGroups_Id`)
LEFT JOIN `UserGroups` on (`ContentAccessRights`.`UserGroups_Id` = `UserGroups`.`Id`)
LEFT JOIN `Users` `Author` on (`Author`.`Id` = `Content`.`Author_Id`)
;

-- -----------------------------------------------------
-- View `SCMS`.`View_ContentData`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `SCMS`.`View_ContentData` ;
DROP TABLE IF EXISTS `SCMS`.`View_ContentData`;
USE `SCMS`;
CREATE  OR REPLACE VIEW `SCMS`.`View_ContentData` AS
SELECT
`ContentData`.`Id` as `Id`,
`ContentTypeFields`.`Name` AS `Name`,
`ContentData`.`Value` as `Value`,
`ContentData`.`ContentTypeFields_Id` as `ContentTypeFields_Id`,
`ContentTypeFields`.`Type` as `Type`,
`ContentDataAccessRights`.`UserGroups_id` as `UserGroup`,
`ContentDataAccessRights`.`Value` as `Access`
FROM `ContentData`
LEFT JOIN `ContentTypeFields` ON (`ContentData`.`ContentTypeFields_Id` = `ContentTypeFields`.`Id`)
LEFT JOIN `ContentDataAccessRights` ON (`ContentTypeFields`.`Id` = `ContentDataAccessRights`.`ContentTypeFields_Id` )
LEFT JOIN `UserGroups` ON (`ContentDataAccessRights`.`UserGroups_Id` = `UserGroups`.`Id`);

-- -----------------------------------------------------
-- View `SCMS`.`View_Users`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `SCMS`.`View_Users` ;
DROP TABLE IF EXISTS `SCMS`.`View_Users`;
USE `SCMS`;
CREATE  OR REPLACE VIEW `SCMS`.`View_Users` AS
SELECT 
`Users`.`Id`,
`Users`.`Email`,
`Users`.`Password`,
`Users`.`Registration`,
`Users`.`LastLogin`,
`Users`.`SSRFEmail`,
`Languages`.`Name` as `LanguageName`,
`Languages`.`Direction` as `LanguageDirection`
FROM `Users`
LEFT JOIN `Languages` ON ( `Users`.`Languages_id` = `Languages`.`id` )

;

-- -----------------------------------------------------
-- View `SCMS`.`View_UserData`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `SCMS`.`View_UserData` ;
DROP TABLE IF EXISTS `SCMS`.`View_UserData`;
USE `SCMS`;
CREATE  OR REPLACE VIEW `SCMS`.`View_UserData` AS
SELECT
`Users_Id` as `UserId`,
`UserDataTypes`.`Name` as `Name`,
`UserDataTypes`.`Type` as `Type`,
`UserData`.`Value` as `Value`
FROM `UserData`
LEFT JOIN `UserDataTypes` On ( `UserData`.`UserDataTypes_Id` = `UserDataTypes`.`Id` )
;

-- -----------------------------------------------------
-- View `SCMS`.`View_UsersInGroups`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `SCMS`.`View_UsersInGroups` ;
DROP TABLE IF EXISTS `SCMS`.`View_UsersInGroups`;
USE `SCMS`;
CREATE  OR REPLACE VIEW `SCMS`.`View_UsersInGroups` AS
SELECT 
	`UsersInGroups`.`Users_id` as `UserId`,
	`Users`.`email` as `email`,
	`Users`.`password` as `password`,
	`Users`.`AuthenticationToken` as `AuthenticationToken`,
	`UserGroups`.`id` as `Id`,
	`UserGroups`.`name` as `Name`,
	`UserGroups`.`created` as `Created`,
	`UserGroups`.`AssignLoggedOutUsers` as `AssignToLoggedOutUsers`
FROM `UsersInGroups`
LEFT JOIN `Users` on ( `UsersInGroups`.`Users_id` = `Users`.`id` )
LEFT JOIN `UserGroups` on ( `UsersInGroups`.`UserGroups_id` = `UserGroups`.`id`);

-- -----------------------------------------------------
-- View `SCMS`.`LoggedOutGroups`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `SCMS`.`LoggedOutGroups` ;
DROP TABLE IF EXISTS `SCMS`.`LoggedOutGroups`;
USE `SCMS`;
CREATE  OR REPLACE VIEW `SCMS`.`LoggedOutGroups` AS
SELECT 
	`UserGroups`.`id` as `id`,
	`UserGroups`.`name` as `name`,
	`UserGroups`.`created` as `created`
FROM `UserGroups`
WHERE `UserGroups`.`assignLoggedOutUsers` = 1
;

-- -----------------------------------------------------
-- View `SCMS`.`LoggedOutContentAccessRights`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `SCMS`.`LoggedOutContentAccessRights` ;
DROP TABLE IF EXISTS `SCMS`.`LoggedOutContentAccessRights`;
USE `SCMS`;
CREATE  OR REPLACE VIEW `SCMS`.`LoggedOutContentAccessRights` AS 
SELECT
`Name` as `UserGroupName`,
`Value` as `Access`,
`UserGroups_id` as `UserGroups_id`,
`ContentTypes_id` as `ContentTypes_id`
FROM `ContentAccessRights`
LEFT JOIN `UserGroups` on (`ContentAccessRights`.`UserGroups_id` = `UserGroups`.`Id`)
WHERE `AssignLoggedOutUsers` = 1;

-- -----------------------------------------------------
-- View `SCMS`.`LoggedOutContentDataAccessRights`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `SCMS`.`LoggedOutContentDataAccessRights` ;
DROP TABLE IF EXISTS `SCMS`.`LoggedOutContentDataAccessRights`;
USE `SCMS`;
CREATE  OR REPLACE VIEW `SCMS`.`LoggedOutContentDataAccessRights` AS 
SELECT
`Name` as `UserGroupName`,
`Value` as `Access`,
`UserGroups_id` as `UserGroups_id`,
`ContentTypeFields_id` as `ContentTypesFields_id`
FROM `ContentDataAccessRights`
LEFT JOIN `UserGroups` on (`ContentDataAccessRights`.`UserGroups_id` = `UserGroups`.`Id`)
WHERE `AssignLoggedOutUsers` = 1;

-- -----------------------------------------------------
-- View `SCMS`.`view_UsersGroups`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `SCMS`.`view_UsersGroups` ;
DROP TABLE IF EXISTS `SCMS`.`view_UsersGroups`;
USE `SCMS`;
CREATE  OR REPLACE VIEW `SCMS`.`view_UsersGroups` AS
SELECT 
`usergroups`.`Name` as `Name`,
`usergroups`.`Created` as `Created`,
`usergroups`.`AssignLoggedOutUsers` as `AssignLoggedOutUsers`,
`UsersInGroups`.`Users_id` as `UsersId`
FROM `usergroups`
LEFT JOIN  `UsersInGroups` on (`usergroups`.`Id` = `usersingroups`.`UserGroups_id`)
;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
