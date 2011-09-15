DROP TABLE IF EXISTS `#__rsProfile`;

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
);
