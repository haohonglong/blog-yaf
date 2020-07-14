-- creat dataBase
DROP DATABASE IF EXISTS `myshop`;
CREATE DATABASE IF NOT EXISTS `myshop` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `myshop`;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` INT (20) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR (29) NOT NULL DEFAULT '' COMMENT '用户注册名是唯一的 只能是邮箱名称',
  `password` VARCHAR (64) NOT NULL COMMENT '用户密码',
  `address` VARCHAR (40) NOT NULL COMMENT '',
  `date` VARCHAR (11) NOT NULL COMMENT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `id` INT (20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` INT (20) unsigned NOT NULL COMMENT 'id of user',
  `type` VARCHAR (4) NOT NULL COMMENT '',
  `amount` INT (10) NOT NULL COMMENT '',
  `total` INT (10) NOT NULL COMMENT '',
  `date` VARCHAR (11) NOT NULL COMMENT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='账单';

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` INT (20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` INT (20) unsigned NOT NULL COMMENT 'id of parent',
  `name` VARCHAR (20) NOT NULL,
  `date` VARCHAR (11) NOT NULL COMMENT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='类别';


DROP TABLE IF EXISTS `goods`;
CREATE TABLE IF NOT EXISTS `goods`(
	`id` INT (20) unsigned NOT NULL AUTO_INCREMENT,
	`cid` INT (20) unsigned NOT NULL COMMENT 'category_id',
  `name` VARCHAR (20) NOT NULL COMMENT '',
  `goodscol` VARCHAR (45) NOT NULL COMMENT '',
  `date` VARCHAR (11) NOT NULL COMMENT '',
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '';


DROP TABLE IF EXISTS `buys`;
CREATE TABLE IF NOT EXISTS `buys`(
	`id` INT (20) unsigned NOT NULL AUTO_INCREMENT,
	`gid` INT (20) unsigned NOT NULL COMMENT 'the id of goods',
  `gname` VARCHAR (20) NOT NULL COMMENT 'the name of goods',
  `date` VARCHAR (11) NOT NULL COMMENT '',
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '';

DROP TABLE IF EXISTS `stocks`;
CREATE TABLE IF NOT EXISTS `stocks`(
	`id` INT (20) unsigned NOT NULL AUTO_INCREMENT,
	`cid` INT (20) unsigned NOT NULL COMMENT 'the id of category',
	`gid` INT (20) unsigned NOT NULL COMMENT 'the id of goods',
	`number` INT (20) unsigned NOT NULL COMMENT '库存数量',
  `date` VARCHAR (11) NOT NULL COMMENT '',
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '库存';

DROP TABLE IF EXISTS `selling`;
CREATE TABLE IF NOT EXISTS `selling`(
	`id` INT (20) unsigned NOT NULL AUTO_INCREMENT,
	`cid` INT (20) unsigned NOT NULL COMMENT 'the id of category',
	`gid` INT (20) unsigned NOT NULL COMMENT 'the id of goods',
  `date` VARCHAR (11) NOT NULL COMMENT '',
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '上架正在出售';

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order`(
	`id` INT (20) unsigned NOT NULL AUTO_INCREMENT,
	`cid` INT (20) unsigned NOT NULL COMMENT 'the id of category',
	`gid` INT (20) unsigned NOT NULL COMMENT 'the id of goods',
	`ster` VARCHAR (45) NOT NULL COMMENT '',
	`state` VARCHAR (10) NOT NULL COMMENT '',
  `date` VARCHAR (11) NOT NULL COMMENT '',
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '订单';












