DROP TABLE IF EXISTS `#__rsProfile`;
DROP TABLE IF EXISTS `#__rsVehicleTypes`;
DROP TABLE IF EXISTS `#__rsUserVehicles`;
DROP TABLE IF EXISTS `#__rsLocations`;
DROP TABLE IF EXISTS `#__rsPaths`;
DROP TABLE IF EXISTS `#__rsTrips`;


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
    '43', '12', '814RCN' , '1999' , NULL , NULL , 'This is my first car'
), (
    '43', '16', 'ABC123' , '1995' , '5' , '0' , 'This baby is my other car'
), (
    '42', '23', 'SY54DM' , NULL , '7' , NULL , 'System Admin car'
);


CREATE TABLE `#__rsLocations` (
    `locationID` INT( 11 ) NOT NULL AUTO_INCREMENT,
    `name` TEXT NULL ,
    PRIMARY KEY ( `locationID` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `rideshare`.`#__rsLocations` (
    `locationID` ,
    `name`
)
VALUES (
    NULL, 'Cairns'
), (
	NULL, 'Townsville'
), (
	NULL, 'Withsunday'
), (
	NULL, 'Mackay'
), (
	NULL, 'Rockhampton'
), (
	NULL, 'Gladstone'
), (
	NULL, 'Bundaberg'
), (
	NULL, 'Sunshine Coast'
), (
	NULL, 'Brisbane'
), (
	NULL, 'Gold Coast'
), (
	NULL, 'Byron Bay'
), (
	NULL, 'Coffs Harbour'
), (
	NULL, 'Port Macquarie'
), (
	NULL, 'Newcastle'
), (
	NULL, 'Sydney'
), (
	NULL, 'Canberra'
), (
	NULL, 'Melbourne'
);

CREATE TABLE `#__rsPaths` (
    `org` INT( 11 ) NOT NULL ,
    `dst` INT( 11 ) NOT NULL ,
    `distance` INT( 4 ) NOT NULL ,
    PRIMARY KEY ( `org`, `dst` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;


/*
    Note to self:
      This insert mechanism will fail on location tables with duplicate
      names. In the backend part, use locationID as reference
*/

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Cairns" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Townsville" LIMIT 1), 
348
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Withsunday" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Townsville" LIMIT 1), 
260
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Withsunday" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Mackay" LIMIT 1), 
135
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Rockhampton" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Mackay" LIMIT 1), 
336
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Rockhampton" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Gladstone" LIMIT 1), 
110
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Bundaberg" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Gladstone" LIMIT 1), 
195
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Bundaberg" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Sunshine Coast" LIMIT 1), 
280
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Brisbane" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Sunshine Coast" LIMIT 1), 
106
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Brisbane" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Gold Coast" LIMIT 1), 
78
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Byron Bay" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Gold Coast" LIMIT 1), 
90
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Byron Bay" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Coffs Harbour" LIMIT 1), 
250
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Port Macquarie" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Coffs Harbour" LIMIT 1), 
160
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Port Macquarie" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Newcastle" LIMIT 1), 
261
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Sydney" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Newcastle" LIMIT 1), 
163
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Sydney" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Canberra" LIMIT 1), 
290
);

INSERT INTO `rideshare`.`#__rsPaths` (`org` , `dst`  , `distance`)
VALUES (
(SELECT locationID FROM `#__rsLocations` WHERE name="Melbourne" LIMIT 1), 
(SELECT locationID FROM `#__rsLocations` WHERE name="Canberra" LIMIT 1), 
666
);

CREATE TABLE `#__rsTrips` (
    `tripID` INT( 11 ) NOT NULL AUTO_INCREMENT,
    `owner` INT NOT NULL, 
    `regno` VARCHAR( 12 ) NOT NULL,
    `origin` INT NOT NULL, 
    `destination` INT NOT NULL, 
    `early` DATE NULL DEFAULT NULL,
    `late` DATE NULL DEFAULT NULL,
    `depart` TIME NULL DEFAULT '00:00:00', 
    PRIMARY KEY ( `tripID` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `rideshare`.`#__rsTrips` (`tripID`, `owner` , `regno`  , `origin`, `destination`)
VALUES (
NULL, 43, '814RCN', 11, 16
);
