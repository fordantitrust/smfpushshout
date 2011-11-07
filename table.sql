 CREATE TABLE IF NOT EXISTS `shoutbox` (
  `id`        int(5) NOT NULL AUTO_INCREMENT,
  `date`      timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userid`    int(11) NOT NULL,
  `username`  varchar(80) NOT NULL,
  `message`   varchar(1024) NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;