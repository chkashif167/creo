<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table {$this->getTable('mksresponsivebannerslider')} (id int not null auto_increment, title varchar(255),image varchar(255),description varchar(255),url varchar(255),imageorder varchar(255),status varchar(255), primary key(id));

INSERT INTO {$this->getTable('mksresponsivebannerslider')} (`title`,`image`,`description`,`imageorder`,`status`) VALUES ('Responsive Banner Slider 1','responsivebannerslider/bannerslider/banner-1.jpg','Responsive Banner Slider 1','0','0'),('Responsive Banner Slider 2','responsivebannerslider/bannerslider/banner-2.jpg','Responsive Banner Slider 2','1','0'),
('Responsive Banner Slider 3','responsivebannerslider/bannerslider/banner-3.jpg','Responsive Banner Slider 3','2','0');
 
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
