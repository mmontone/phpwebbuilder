<?php
$setup_sql =
"CREATE TABLE `MenuItem` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `PWBversion` int(11) unsigned NOT NULL default '0',
  `name` text,
  `controller` text,
  `params` text,
  `section` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `indexMenuItem` (`name`(50),`section`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `MenuItem` (`id`, `PWBversion`, `name`, `controller`, `params`, `section`) VALUES (1, 0, 'Database Administration', 'DBController', '', 1),
(2, 0, 'Roles Administration', 'RolesController', '', 2);

CREATE TABLE `MenuSection` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `PWBversion` int(11) unsigned NOT NULL default '0',
  `name` text,
  `menuorder` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `indexMenuSection` (`name`(50),`menuorder`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `MenuSection` (`id`, `PWBversion`, `name`, `menuorder`) VALUES (1, 0, 'Database Structure', 0),
(2, 0, 'User Admin', 0);

CREATE TABLE `Role` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `PWBversion` int(11) unsigned NOT NULL default '0',
  `name` text,
  `description` longtext,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `indexRole` (`name`(50))
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `Role` (`id`, `PWBversion`, `name`, `description`) VALUES (1, 0, 'Superuser', ''),
(2, 0, 'Guest', '');

CREATE TABLE `RolePermission` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `PWBversion` int(11) unsigned NOT NULL default '0',
  `role` int(11) default NULL,
  `permission` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `indexRolePermission` (`role`,`permission`(50))
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

INSERT INTO `RolePermission` (`id`, `PWBversion`, `role`, `permission`) VALUES (1, 0, 1, 'DatabaseAdmin'),
(2, 0, 1, '*'),
(3, 0, 1, 'UserAdmin');

CREATE TABLE `UserRole` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `PWBversion` int(11) unsigned NOT NULL default '0',
  `user` int(11) default NULL,
  `role` int(11) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `indexUserRole` (`user`,`role`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `UserRole` (`id`, `PWBversion`, `user`, `role`) VALUES (1, 0, 1, 1),
(2, 0, 2, 2);

CREATE TABLE `session` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `PWBversion` int(11) unsigned NOT NULL default '0',
  `session_name` text,
  `session_id` text,
  `date_created` datetime default NULL,
  `last_updated` datetime default NULL,
  `session_data` longblob,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `indexsession` (`session_name`(50),`session_id`(50),`date_created`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `PWBversion` int(11) unsigned NOT NULL default '0',
  `user` text,
  `pass` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `indexusers` (`user`(50))
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `users` (`id`, `PWBversion`, `user`, `pass`) VALUES (1, 0, 'admin', 'PWB-admin'),
(2, 0, 'guest', 'guest');
";
?>
