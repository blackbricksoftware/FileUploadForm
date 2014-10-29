CREATE TABLE IF NOT EXISTS `#__hb_members` (
	`m_id` INTEGER NOT NULL AUTO_INCREMENT,
	`m_uid` INTEGER NOT NULL,
	`m_time` INTEGER NOT NULL,
	`m_update` INTEGER NOT NULL,
	
	`m_address` TINYTEXT NOT NULL,
	`m_city` TINYTEXT NOT NULL,
	`m_state` TINYTEXT NOT NULL,
	`m_zip` TINYTEXT NOT NULL,
	`m_phone` TINYTEXT NOT NULL,
	
	`m_bmonth` INTEGER NOT NULL,
	`m_bday` INTEGER NOT NULL,
	
	`m_amonth` INTEGER NOT NULL,
	`m_aday` INTEGER NOT NULL,
	
	`m_duespaid` INTEGER NOT NULL,
	
	PRIMARY KEY (`m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
