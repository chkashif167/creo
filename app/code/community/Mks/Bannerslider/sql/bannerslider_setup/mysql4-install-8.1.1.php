<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
create table {$this->getTable('popupgalleryslider')}(id int not null auto_increment, title varchar(255),  image varchar(255), url varchar(255),  description varchar(255), status varchar(100), primary key(id));


insert into {$this->getTable('popupgalleryslider')} (`title`,`image`,`description`,`url`,`status`) VALUES 
('Image gallery  1','bannerslider/imagegallery/3.jpg','Image gallery  1','www.google.com','0'), 
('Image gallery  2','bannerslider/imagegallery/4.jpg','Image gallery  2','www.google.com','0'),
('Image gallery  3','bannerslider/imagegallery/5.jpg','Image gallery  3','www.google.com','0'),
('Image gallery  4','bannerslider/imagegallery/6.jpg','Image gallery  4','www.google.com','0'),
('Image gallery  5','bannerslider/imagegallery/7.jpg','Image gallery  5','www.google.com','0'),
('Image gallery  6','bannerslider/imagegallery/8.jpg','Image gallery  6','www.google.com','0'),
('Image gallery  7','bannerslider/imagegallery/9.jpg','Image gallery  7','www.google.com','0'),
('Image gallery  8','bannerslider/imagegallery/10.jpg','Image gallery  8','www.google.com','0'),
('Image gallery  9','bannerslider/imagegallery/11.jpg','Image gallery  9','www.google.com','0'),
('Image gallery  10','bannerslider/imagegallery/2.jpg','Image gallery  10','www.google.com','0'),
('Image gallery  11','bannerslider/imagegallery/3.jpg','Image gallery  11','www.google.com','0'), 
('Image gallery  12','bannerslider/imagegallery/4.jpg','Image gallery  12','www.google.com','0'),
('Image gallery  13','bannerslider/imagegallery/5.jpg','Image gallery  13','www.google.com','0'),
('Image gallery  14','bannerslider/imagegallery/6.jpg','Image gallery  14','www.google.com','0'),
('Image gallery  15','bannerslider/imagegallery/7.jpg','Image gallery  15','www.google.com','0'),
('Image gallery  16','bannerslider/imagegallery/8.jpg','Image gallery  16','www.google.com','0'),
('Image gallery  17','bannerslider/imagegallery/9.jpg','Image gallery  17','www.google.com','0'),
('Image gallery  18','bannerslider/imagegallery/10.jpg','Image gallery  18','www.google.com','0'),
('Image gallery  19','bannerslider/imagegallery/11.jpg','Image gallery  19','www.google.com','0'),
('Image gallery  20','bannerslider/imagegallery/2.jpg','Image gallery  20','www.google.com','0');
		
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 
