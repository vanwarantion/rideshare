DROP TABLE IF EXISTS `#__rsProfile`;
DROP TABLE IF EXISTS `#__rsVehicleTypes`;
DROP TABLE IF EXISTS `#__rsUserVehicles`;

CREATE TABLE `#__rsProfile` (
    `userID` INT( 11 ) NOT NULL ,
    `description` TEXT NULL ,
    PRIMARY KEY ( `userID` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `rideshare`.`#__rsProfile` (
    `userID` ,
    `description`
)
VALUES (
    '43', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque non pulvinar velit. Ut vehicula dignissim venenatis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Etiam vestibulum posuere libero, vitae ultrices elit mattis at. Nunc vitae dui ut tortor lacinia elementum. Phasellus vestibulum pellentesque hendrerit. Duis a lacinia elit.'
), (
    '42', 'This is system admin'
);

CREATE TABLE `#__rsVehicleTypes` (
    `ID` INT( 11 ) NOT NULL AUTO_INCREMENT,
    `text` VARCHAR( 50 ) NOT NULL ,
    `catID` INT( 11 ) NOT NULL DEFAULT '0',
    `seats` INT( 2 ) NULL ,
    `aircon` INT( 1 ) NULL ,
    PRIMARY KEY ( `ID` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (
    `ID` ,
    `text` ,
    `catID` ,
    `seats` ,
    `aircon`
    )
VALUES (
    NULL , 'Ford', NULL, NULL, NULL
), (
    NULL , 'Mitsubishi', NULL, NULL, NULL
), (
    NULL , 'Toyota', NULL, NULL, NULL
), (
    NULL , 'Honda', NULL, NULL, NULL
), (
    NULL , 'Mazda', NULL, NULL, NULL
), (
    NULL , 'Nissan', NULL, NULL, NULL
);

/*
# Ford:
*/
INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Falcon', ID, 5, 1 FROM `#__rsVehicleTypes` WHERE text="Ford" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Fairlane', ID, 5, 1 FROM `#__rsVehicleTypes` WHERE text="Ford" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Explorer', ID, 5, 1 FROM `#__rsVehicleTypes` WHERE text="Ford" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Focus', ID, 4, 1 FROM `#__rsVehicleTypes` WHERE text="Ford" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Fiesta', ID, 4, 0 FROM `#__rsVehicleTypes` WHERE text="Ford" LIMIT 1;

/*
# Mitsubishi:
*/
INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Magna', ID, 5, 1 FROM `#__rsVehicleTypes` WHERE text="Mitsubishi" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Lancer', ID, 4, 1 FROM `#__rsVehicleTypes` WHERE text="Mitsubishi" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Colt', ID, 4, 0 FROM `#__rsVehicleTypes` WHERE text="Mitsubishi" LIMIT 1;

/*
# Toyota:
*/
INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Camry', ID, 5, 1 FROM `#__rsVehicleTypes` WHERE text="Toyota" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Yaris', ID, 4, 1 FROM `#__rsVehicleTypes` WHERE text="Toyota" LIMIT 1;

/*
# Honda:
*/
INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Accord', ID, 5, 1 FROM `#__rsVehicleTypes` WHERE text="Honda" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Civic', ID, 4, 0 FROM `#__rsVehicleTypes` WHERE text="Honda" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Perilude', ID, 2, 1 FROM `#__rsVehicleTypes` WHERE text="Honda" LIMIT 1;

/*
# Mazda
*/
INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , '6', ID, 4, 1 FROM `#__rsVehicleTypes` WHERE text="Mazda" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , '3', ID, 4, 1 FROM `#__rsVehicleTypes` WHERE text="Mazda" LIMIT 1;

/*
# Nissan
*/
INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Pulsar', ID, 4, 1 FROM `#__rsVehicleTypes` WHERE text="Nissan" LIMIT 1;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (`ID` , `text`  , `catID` ,  `seats` , `aircon`)
SELECT NULL , 'Skystar', ID, 5, 1 FROM `#__rsVehicleTypes` WHERE text="Nissan" LIMIT 1;


CREATE TABLE `#__rsUserVehicles` (
    `userID` INT( 11 ) NOT NULL ,
    `vtypeID` INT( 11 ) NOT NULL ,
    `regno` VARCHAR( 12 ) NOT NULL,
    `vyear` INT( 4 ) NULL ,
    `seats` INT( 2 ) NULL ,
    `aircon` INT( 1 ) NULL ,
    `description` TEXT NULL, 
    PRIMARY KEY ( `regno` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `rideshare`.`#__rsUserVehicles` (
    `userID` , 
    `vtypeID` , 
    `regno` , 
    `vyear` , 
    `seats` , 
    `aircon` , 
    `description` 
) VALUES (
    '43', '4', '814RCN' , '1999' , NULL , NULL , 'This is my first car'
), (
    '43', '5', 'ABC123' , '1995' , '5' , '0' , 'This baby is my other car'
), (
    '42', '1', 'SY54DM' , NULL , NULL , NULL , 'System Admin car'
);
