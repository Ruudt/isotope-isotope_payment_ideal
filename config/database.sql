-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- 
-- Table `tl_iso_payment_modules`
-- 

CREATE TABLE `tl_iso_payment_modules` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ideal_sub_id` varchar(32) NOT NULL default '',
  `ideal_acceptant_id` varchar(9) NOT NULL default '',
  `ideal_acceptant_key` varchar(64) NOT NULL default '',
  `ideal_url_test` varchar(255) NOT NULL default '',
  `ideal_url_production` varchar(255) NOT NULL default '',
  `ideal_unique_urls` char(1) NOT NULL default '',
  `ideal_transaction_url_test` varchar(255) NOT NULL default '',
  `ideal_transaction_url_production` varchar(255) NOT NULL default '',
  `ideal_status_url_test` varchar(255) NOT NULL default '',
  `ideal_status_url_production` varchar(255) NOT NULL default '',
  `ideal_testmode` char(1) NOT NULL default '',
  `ideal_url_service` int(10) unsigned NOT NULL default '0',
  `ideal_acceptant_key_file` varchar(255) NOT NULL default '',  
  `ideal_priv_cert_file` varchar(255) NOT NULL default '',
  `ideal_publ_cert_file` varchar(255) NOT NULL default '',
  `ideal_use_publ_test_cert_file` char(1) NOT NULL default '',
  `ideal_publ_test_cert_file` varchar(255) NOT NULL default '',
  `ideal_cache_path` varchar(255) NOT NULL default '',
  `ideal_order_description` varchar(64) NOT NULL default '',
  `ideal_basic_accept_status_risk` char(1) NOT NULL default '',
  `ideal_verbose_logging` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 
-- Table `tl_iso_orders`
-- 

CREATE TABLE `tl_iso_orders` (
  `ideal_transaction_id` varchar(16) NOT NULL default '',
  `ideal_entrance_code` varchar(40) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
