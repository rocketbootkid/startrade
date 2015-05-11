CREATE DATABASE `startrade` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE startrade;

CREATE TABLE `cargohold` (
  `player_id` int(11) NOT NULL,
  `commodity_id` int(11) default NULL,
  `amount` int(11) default NULL,
  `bought_for` int(11) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `commodity` (
  `commodity_id` int(11) NOT NULL auto_increment,
  `commodity_name` varchar(45) default NULL,
  `best_planet_type` varchar(45) default NULL,
  `min_price` int(11) default NULL,
  `max_price` int(11) default NULL,
  `size` int(11) default NULL,
  PRIMARY KEY  (`commodity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `marketplace` (
  `marketplace_id` int(11) NOT NULL auto_increment,
  `planet_id` int(11) default NULL,
  `commodity_id` int(11) default NULL,
  `commodity_unit_cost` int(11) default NULL,
  `commodity_units` int(11) default NULL,
  PRIMARY KEY  (`marketplace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `planets` (
  `planet_id` int(11) NOT NULL auto_increment,
  `planet_name` varchar(45) default NULL,
  `system_id` int(11) default NULL,
  `planet_type` varchar(45) default NULL,
  PRIMARY KEY  (`planet_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `player` (
  `player_id` int(11) NOT NULL auto_increment,
  `ship_name` varchar(45) default NULL,
  `current_funds` int(11) default NULL,
  `current_planet` varchar(45) default NULL,
  `remaining_fuel` int(11) default NULL,
  PRIMARY KEY  (`player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `ships` (
  `ship_id` int(11) NOT NULL auto_increment,
  `ship_name` varchar(45) default NULL,
  `ship_cargo` int(11) default NULL,
  `player_id` int(11) default NULL,
  PRIMARY KEY  (`ship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `systems` (
  `system_id` int(11) NOT NULL auto_increment,
  `system_name` varchar(45) default NULL,
  `galaxy_id` int(11) default NULL,
  `galaxy_name` varchar(45) default NULL,
  PRIMARY KEY  (`system_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;