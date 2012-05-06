ALTER TABLE `oc_affiliate` CHANGE `ip` `ip` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `oc_customer` CHANGE `ip` `ip` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `oc_customer_ip` CHANGE `ip` `ip` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `oc_customer_ip_blacklist` CHANGE `ip` `ip` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `oc_order` CHANGE `ip` `ip` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `oc_order` CHANGE `forwarded_ip` `forwarded_ip` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `oc_user` CHANGE `ip` `ip` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

