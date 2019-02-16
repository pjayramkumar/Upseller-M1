<?php

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('upseller_clouldsearch/queue')}` (
  `qjob_id` BIGINT(20) NOT NULL auto_increment,
  `qpid` int(20) NULL,
  `qpriority` int(11) NOT NULL DEFAULT 0,
  `qclass` varchar(50) NOT NULL,
  `qmethod` varchar(50) NOT NULL,
  `qdata` longtext NOT NULL DEFAULT '',
  `qdata_size` int(11) NOT NULL DEFAULT 5,
  `qstore_id` int(11) NULL,
  `qmax_retries` int(11) NOT NULL DEFAULT 3,
  `qretries` int(11) NOT NULL DEFAULT 0,
  `qerror_log` text NOT NULL DEFAULT '',
  `qstatus` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL,
  `updated_at` DATETIME NOT NULL,
  PRIMARY KEY `qjob_id` (`qjob_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8 AUTO_INCREMENT=1;
");

$installer->endSetup();
