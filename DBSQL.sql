SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `applications`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `applications` (
  `AppId` INT(11) NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(80) NULL DEFAULT NULL ,
  `Key` VARCHAR(80) NULL DEFAULT NULL ,
  PRIMARY KEY (`AppId`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `languages`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `languages` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(45) NULL DEFAULT NULL ,
  `Direction` VARCHAR(3) NULL DEFAULT NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `users` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT ,
  `Email` VARCHAR(60) NULL DEFAULT NULL ,
  `Password` VARCHAR(100) NULL DEFAULT NULL ,
  `Registration` DATETIME NULL DEFAULT NULL ,
  `LastLogin` DATETIME NULL DEFAULT NULL ,
  `SSRFEmail` VARCHAR(60) NULL DEFAULT NULL ,
  `Languages_id` INT(11) NOT NULL ,
  `AuthenticationToken` VARCHAR(100) NULL DEFAULT NULL ,
  `Admin` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `fk_Users_Languages1_idx` (`Languages_id` ASC) ,
  CONSTRAINT `fk_Users_Languages1`
    FOREIGN KEY (`Languages_id` )
    REFERENCES `languages` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `authkeys`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `authkeys` (
  `Users_Id` INT(11) NOT NULL ,
  `Applications_AppId` INT(11) NOT NULL ,
  `Key` VARCHAR(80) NULL DEFAULT NULL ,
  `Expire Date` BIGINT(20) NULL DEFAULT NULL ,
  PRIMARY KEY (`Users_Id`, `Applications_AppId`) ,
  INDEX `fk_AuthKeys_Users1_idx` (`Users_Id` ASC) ,
  INDEX `fk_AuthKeys_Applications1_idx` (`Applications_AppId` ASC) ,
  CONSTRAINT `fk_AuthKeys_Applications1`
    FOREIGN KEY (`Applications_AppId` )
    REFERENCES `applications` (`AppId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AuthKeys_Users1`
    FOREIGN KEY (`Users_Id` )
    REFERENCES `users` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `contenttypes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `contenttypes` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 8
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `content`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `content` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `Parent_id` INT(11) NULL DEFAULT NULL ,
  `URL` VARCHAR(100) NULL DEFAULT NULL ,
  `ContentTypes_id` INT(11) NOT NULL ,
  `Users_id` INT(11) NOT NULL ,
  `Applications_AppId` INT(11) NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_Content_ContentTypes1_idx` (`ContentTypes_id` ASC) ,
  INDEX `fk_Content_Users1_idx` (`Users_id` ASC) ,
  INDEX `fk_Content_Applications1_idx` (`Applications_AppId` ASC) ,
  CONSTRAINT `fk_Content_Applications1`
    FOREIGN KEY (`Applications_AppId` )
    REFERENCES `applications` (`AppId` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Content_ContentTypes1`
    FOREIGN KEY (`ContentTypes_id` )
    REFERENCES `contenttypes` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Content_Users1`
    FOREIGN KEY (`Users_id` )
    REFERENCES `users` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 47
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `usergroups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `usergroups` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(45) NOT NULL ,
  `Created` DATETIME NOT NULL ,
  `AssignLoggedOutUsers` BIT(1) NOT NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `contentaccessrights`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `contentaccessrights` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT ,
  `UserGroups_id` INT(11) NOT NULL ,
  `Value` VARCHAR(4) NULL DEFAULT NULL ,
  `ContentTypes_id` INT(11) NOT NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `fk_ContentAccessRights_UserGroups1_idx` (`UserGroups_id` ASC) ,
  INDEX `fk_ContentAccessRights_ContentTypes1_idx` (`ContentTypes_id` ASC) ,
  CONSTRAINT `fk_ContentAccessRights_ContentTypes1`
    FOREIGN KEY (`ContentTypes_id` )
    REFERENCES `contenttypes` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ContentAccessRights_UserGroups1`
    FOREIGN KEY (`UserGroups_id` )
    REFERENCES `usergroups` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 34
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `contentlang`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `contentlang` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT ,
  `Content_id` INT(11) NOT NULL ,
  `Languages_id` INT(11) NOT NULL ,
  `Title` VARCHAR(50) NULL DEFAULT NULL ,
  `Keywords` TEXT NOT NULL ,
  `Description` TEXT NULL DEFAULT NULL ,
  `Created` DATETIME NULL DEFAULT NULL ,
  `LastModified` DATETIME NULL DEFAULT NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `fk_ContentLang_Content1_idx` (`Content_id` ASC) ,
  INDEX `fk_ContentLang_Languages1_idx` (`Languages_id` ASC) ,
  CONSTRAINT `fk_ContentLang_Content1`
    FOREIGN KEY (`Content_id` )
    REFERENCES `content` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ContentLang_Languages1`
    FOREIGN KEY (`Languages_id` )
    REFERENCES `languages` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 33
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `contenttypefields`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `contenttypefields` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `ContentTypes_id` INT(11) NOT NULL ,
  `Name` VARCHAR(45) NULL DEFAULT NULL ,
  `Type` VARCHAR(45) NULL DEFAULT NULL ,
  `TypeData` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_ContentTypeFields_ContentTypes1_idx` (`ContentTypes_id` ASC) ,
  CONSTRAINT `fk_ContentTypeFields_ContentTypes1`
    FOREIGN KEY (`ContentTypes_id` )
    REFERENCES `contenttypes` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 16
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `contentdata`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `contentdata` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT ,
  `ContentLang_id` INT(11) NOT NULL ,
  `ContentTypeFields_id` INT(11) NOT NULL ,
  `Value` LONGTEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`Id`) ,
  INDEX `fk_ContentData_ContentTypeFields1_idx` (`ContentTypeFields_id` ASC) ,
  INDEX `fk_ContentData_ContentLang1_idx` (`ContentLang_id` ASC) ,
  CONSTRAINT `fk_ContentData_ContentLang1`
    FOREIGN KEY (`ContentLang_id` )
    REFERENCES `contentlang` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ContentData_ContentTypeFields1`
    FOREIGN KEY (`ContentTypeFields_id` )
    REFERENCES `contenttypefields` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 48
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `contentdataaccessrights`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `contentdataaccessrights` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT ,
  `ContentTypeFields_id` INT(11) NOT NULL ,
  `UserGroups_id` INT(11) NOT NULL ,
  `Value` VARCHAR(4) NULL DEFAULT NULL ,
  PRIMARY KEY (`Id`, `UserGroups_id`, `ContentTypeFields_id`) ,
  INDEX `fk_ContentDataAccessRights_UserGroups1_idx` (`UserGroups_id` ASC) ,
  INDEX `fk_ContentDataAccessRights_ContentTypeFields1_idx` (`ContentTypeFields_id` ASC) ,
  CONSTRAINT `fk_ContentDataAccessRights_ContentTypeFields1`
    FOREIGN KEY (`ContentTypeFields_id` )
    REFERENCES `contenttypefields` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ContentDataAccessRights_UserGroups1`
    FOREIGN KEY (`UserGroups_id` )
    REFERENCES `usergroups` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 46
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `userdatatypes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `userdatatypes` (
  `Id` INT(11) NOT NULL AUTO_INCREMENT ,
  `Name` VARCHAR(45) NULL DEFAULT NULL ,
  `Type` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`Id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `userdata`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `userdata` (
  `Id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `UserDataTypes_id` INT(11) NOT NULL ,
  `Users_id` INT(11) NOT NULL ,
  `Value` TEXT NULL DEFAULT NULL ,
  PRIMARY KEY (`Id`) ,
  UNIQUE INDEX `id_UNIQUE` (`Id` ASC) ,
  INDEX `fk_UserData_UserDataTypes1_idx` (`UserDataTypes_id` ASC) ,
  INDEX `fk_UserData_Users1_idx` (`Users_id` ASC) ,
  CONSTRAINT `fk_UserData_UserDataTypes1`
    FOREIGN KEY (`UserDataTypes_id` )
    REFERENCES `userdatatypes` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UserData_Users1`
    FOREIGN KEY (`Users_id` )
    REFERENCES `users` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `usersingroups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `usersingroups` (
  `Users_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `UserGroups_id` INT(11) NOT NULL ,
  INDEX `fk_UsersInGroups_Users_idx` (`Users_id` ASC) ,
  INDEX `fk_UsersInGroups_usergroups1_idx` (`UserGroups_id` ASC) ,
  CONSTRAINT `fk_UsersInGroups_Users`
    FOREIGN KEY (`Users_id` )
    REFERENCES `users` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UsersInGroups_usergroups1`
    FOREIGN KEY (`UserGroups_id` )
    REFERENCES `usergroups` (`Id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Placeholder table for view `loggedoutcontentaccessrights`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `loggedoutcontentaccessrights` (`UserGroupName` INT, `Access` INT, `UserGroups_id` INT, `ContentTypes_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `loggedoutcontentdataaccessrights`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `loggedoutcontentdataaccessrights` (`UserGroupName` INT, `Access` INT, `UserGroups_id` INT, `ContentTypesFields_id` INT);

-- -----------------------------------------------------
-- Placeholder table for view `loggedoutgroups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `loggedoutgroups` (`Id` INT, `name` INT, `created` INT);

-- -----------------------------------------------------
-- Placeholder table for view `view_contenttypefields`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `view_contenttypefields` (`Id` INT, `Name` INT, `Type` INT, `TypeData` INT, `contenttypes_id` INT, `contenttypes_name` INT);

-- -----------------------------------------------------
-- Placeholder table for view `view_languagecontent`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `view_languagecontent` (`UserId` INT, `UserGroupId` INT, `UserGroupName` INT, `AccessRights` INT, `ContentId` INT, `Parent` INT, `URL` INT, `TypeId` INT, `TypeName` INT, `AuthorId` INT, `AuthorEmail` INT, `LanguageId` INT, `LanguageName` INT, `LanguageDirection` INT, `ContentLangId` INT, `ContentTitle` INT, `ContentDescription` INT, `DateCreated` INT, `LastModified` INT);

-- -----------------------------------------------------
-- Placeholder table for view `view_languagecontentdata`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `view_languagecontentdata` (`ContentId` INT, `title` INT, `ContentLang_id` INT, `Name` INT, `Content` INT, `Type` INT, `UserGroup` INT, `Access` INT);

-- -----------------------------------------------------
-- Placeholder table for view `view_userdata`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `view_userdata` (`UserId` INT, `Name` INT, `Type` INT, `Value` INT);

-- -----------------------------------------------------
-- Placeholder table for view `view_usersgroups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `view_usersgroups` (`Id` INT, `Name` INT, `Created` INT, `AssignLoggedOutUsers` INT, `UserId` INT);

-- -----------------------------------------------------
-- Placeholder table for view `view_usersingroups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `view_usersingroups` (`UserId` INT, `email` INT, `password` INT, `AuthenticationToken` INT, `Id` INT, `Name` INT, `Created` INT, `AssignToLoggedOutUsers` INT);

-- -----------------------------------------------------
-- View `loggedoutcontentaccessrights`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `loggedoutcontentaccessrights`;
CREATE  OR REPLACE VIEW `loggedoutcontentaccessrights` AS select `usergroups`.`Name` AS `UserGroupName`,`contentaccessrights`.`Value` AS `Access`,`contentaccessrights`.`UserGroups_id` AS `UserGroups_id`,`contentaccessrights`.`ContentTypes_id` AS `ContentTypes_id` from (`contentaccessrights` left join `usergroups` on((`contentaccessrights`.`UserGroups_id` = `usergroups`.`Id`))) where (`usergroups`.`AssignLoggedOutUsers` = 1);

-- -----------------------------------------------------
-- View `loggedoutcontentdataaccessrights`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `loggedoutcontentdataaccessrights`;
CREATE  OR REPLACE VIEW `loggedoutcontentdataaccessrights` AS select `usergroups`.`Name` AS `UserGroupName`,`contentdataaccessrights`.`Value` AS `Access`,`contentdataaccessrights`.`UserGroups_id` AS `UserGroups_id`,`contentdataaccessrights`.`ContentTypeFields_id` AS `ContentTypesFields_id` from (`contentdataaccessrights` left join `usergroups` on((`contentdataaccessrights`.`UserGroups_id` = `usergroups`.`Id`))) where (`usergroups`.`AssignLoggedOutUsers` = 1);

-- -----------------------------------------------------
-- View `loggedoutgroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `loggedoutgroups`;
CREATE  OR REPLACE VIEW `loggedoutgroups` AS select `usergroups`.`Id` AS `Id`,`usergroups`.`Name` AS `name`,`usergroups`.`Created` AS `created` from `usergroups` where (`usergroups`.`AssignLoggedOutUsers` = 1);

-- -----------------------------------------------------
-- View `view_contenttypefields`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `view_contenttypefields`;
CREATE  OR REPLACE VIEW `view_contenttypefields` AS select `contenttypefields`.`id` AS `Id`,`contenttypefields`.`Name` AS `Name`,`contenttypefields`.`Type` AS `Type`,`contenttypefields`.`TypeData` AS `TypeData`,`contenttypes`.`Id` AS `contenttypes_id`,`contenttypes`.`Name` AS `contenttypes_name` from (`contenttypefields` left join `contenttypes` on((`contenttypefields`.`ContentTypes_id` = `contenttypes`.`Id`)));

-- -----------------------------------------------------
-- View `view_languagecontent`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `view_languagecontent`;
CREATE  OR REPLACE VIEW `view_languagecontent` AS select `usersingroups`.`Users_id` AS `UserId`,`contentaccessrights`.`UserGroups_id` AS `UserGroupId`,`usergroups`.`Name` AS `UserGroupName`,`contentaccessrights`.`Value` AS `AccessRights`,`content`.`id` AS `ContentId`,`content`.`Parent_id` AS `Parent`,`content`.`URL` AS `URL`,`content`.`ContentTypes_id` AS `TypeId`,`contenttypes`.`Name` AS `TypeName`,`content`.`Users_id` AS `AuthorId`,`author`.`Email` AS `AuthorEmail`,`contentlang`.`Languages_id` AS `LanguageId`,`languages`.`Name` AS `LanguageName`,`languages`.`Direction` AS `LanguageDirection`,`contentlang`.`Id` AS `ContentLangId`,`contentlang`.`Title` AS `ContentTitle`,`contentlang`.`Description` AS `ContentDescription`,`contentlang`.`Created` AS `DateCreated`,`contentlang`.`LastModified` AS `LastModified` from (((((((((`content` left join `applications` on((`content`.`Applications_AppId` = `applications`.`AppId`))) left join `authkeys` on((`content`.`Applications_AppId` = `authkeys`.`Applications_AppId`))) left join `contentlang` on((`contentlang`.`Content_id` = `content`.`id`))) left join `contenttypes` on((`content`.`ContentTypes_id` = `contenttypes`.`Id`))) left join `languages` on((`contentlang`.`Languages_id` = `languages`.`Id`))) left join `contentaccessrights` on((`content`.`ContentTypes_id` = `contentaccessrights`.`ContentTypes_id`))) left join `usersingroups` on((`contentaccessrights`.`UserGroups_id` = `usersingroups`.`UserGroups_id`))) left join `usergroups` on((`contentaccessrights`.`UserGroups_id` = `usergroups`.`Id`))) left join `users` `author` on((`author`.`Id` = `content`.`Users_id`)));

-- -----------------------------------------------------
-- View `view_languagecontentdata`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `view_languagecontentdata`;
CREATE  OR REPLACE VIEW `view_languagecontentdata` AS select `contentlang`.`Content_id` AS `ContentId`,`contentlang`.`Title` AS `title`,`contentlang`.`Id` AS `ContentLang_id`,`contenttypefields`.`Name` AS `Name`,`contentdata`.`Value` AS `Content`,`contenttypefields`.`Type` AS `Type`,`contentdataaccessrights`.`UserGroups_id` AS `UserGroup`,`contentdataaccessrights`.`Value` AS `Access` from ((((`contentdata` left join `contentlang` on((`contentdata`.`ContentLang_id` = `contentlang`.`Id`))) left join `contenttypefields` on((`contentdata`.`ContentTypeFields_id` = `contenttypefields`.`id`))) left join `contentdataaccessrights` on((`contenttypefields`.`id` = `contentdataaccessrights`.`ContentTypeFields_id`))) left join `usergroups` on((`contentdataaccessrights`.`UserGroups_id` = `usergroups`.`Id`)));

-- -----------------------------------------------------
-- View `view_userdata`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `view_userdata`;
CREATE  OR REPLACE VIEW `view_userdata` AS select `userdata`.`Users_id` AS `UserId`,`userdatatypes`.`Name` AS `Name`,`userdatatypes`.`Type` AS `Type`,`userdata`.`Value` AS `Value` from (`userdata` left join `userdatatypes` on((`userdata`.`UserDataTypes_id` = `userdatatypes`.`Id`)));

-- -----------------------------------------------------
-- View `view_usersgroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `view_usersgroups`;
CREATE  OR REPLACE VIEW `view_usersgroups` AS select `usergroups`.`Id` AS `Id`,`usergroups`.`Name` AS `Name`,`usergroups`.`Created` AS `Created`,`usergroups`.`AssignLoggedOutUsers` AS `AssignLoggedOutUsers`,`usersingroups`.`Users_id` AS `UserId` from (`usergroups` left join `usersingroups` on((`usergroups`.`Id` = `usersingroups`.`UserGroups_id`)));

-- -----------------------------------------------------
-- View `view_usersingroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `view_usersingroups`;
CREATE  OR REPLACE VIEW `view_usersingroups` AS select `usersingroups`.`Users_id` AS `UserId`,`users`.`Email` AS `email`,`users`.`Password` AS `password`,`users`.`AuthenticationToken` AS `AuthenticationToken`,`usergroups`.`Id` AS `Id`,`usergroups`.`Name` AS `Name`,`usergroups`.`Created` AS `Created`,`usergroups`.`AssignLoggedOutUsers` AS `AssignToLoggedOutUsers` from ((`usersingroups` left join `users` on((`usersingroups`.`Users_id` = `users`.`Id`))) left join `usergroups` on((`usersingroups`.`UserGroups_id` = `usergroups`.`Id`)));


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
