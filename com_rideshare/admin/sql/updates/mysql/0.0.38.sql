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
    `make` VARCHAR( 30 ) NOT NULL ,
    `model` VARCHAR( 30 ) NOT NULL ,
    `seats` INT( 2 ) NULL ,
    `aircon` INT( 1 ) NULL ,
    PRIMARY KEY ( `ID` )
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `rideshare`.`#__rsVehicleTypes` (
    `ID` ,
    `make` ,
    `model` ,
    `seats` ,
    `aircon`
    )
VALUES (
    NULL , 'Ford', 'Falcon', '5', '1'
), (
    NULL , 'Ford', 'Laser', '4', '0'
), (
    NULL , 'Ford', 'Fairlane', '5', '1'
), (
    NULL , 'Mitsubishi', 'Magna', '5', '1'
), (
    NULL , 'Mitsubishi', 'Lancer', '4', '1'
), (
    NULL , 'Toyota', 'Corolla', '4', '0'
), (
    NULL , 'Toyota', 'Camry', '5', '1'
), (
    NULL , 'Honda', 'Accord', '5', '1'
), (
    NULL , 'Honda', 'Civic', '4', '0'
), (
    NULL , 'Honda', 'Jazz', '4', '1'
), (
    NULL , 'Mazda', '3', '4', '1'
), (
    NULL , 'Mazda', '6', '5', '1'
), (
    NULL , 'Nissan', 'Pulsar', '4', '0'
);


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
