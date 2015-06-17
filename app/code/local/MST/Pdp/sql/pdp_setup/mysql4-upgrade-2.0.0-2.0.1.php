<?php
$installer = $this;
$installer->startSetup();
$installer->run("
    INSERT INTO {$this->getTable('mst_pdp_artwork_category')} (`id`, `title`, `status`, `position`) VALUES
	(1, 'Animals', 1, 0),
	(2, 'Christmas', 1, 2),
	(3, 'Sport', 1, 6),
	(4, 'Love', 1, 4),
	(5, 'Flag', 1, 4),
	(6, 'Memes', 1, 5),
	(7, 'Sticker', 1, 7),
	(9, 'Logo', 1, 4),
	(10, 'Colorful label', 1, 3),
	(11, 'Social Icon', 1, 9);
    
    INSERT INTO {$this->getTable('mst_pdp_fonts')} (`name`, `ext`) VALUES
	('gooddog', 'otf'),
	('lobster', 'otf'),
	('lokicola', 'ttf'),
	('madewithb', 'ttf'),
	('montague', 'ttf'),
	('organo', 'ttf'),
	('playball', 'ttf'),
	('riesling', 'ttf'),
	('trocadero', 'ttf');
    
    INSERT INTO {$this->getTable('mst_pdp_colors')} (`color_name`, `color_code`, `status`, `position`) VALUES
	('White ', 'FFFFFF', 1, 1),
	('Cream ', 'F2F3E2', 1, 2),
	('Sulfur-yellow ', 'FDF027', 1, 3),
	('Yellow ', 'FAD431', 1, 4),
	('Golden yellow ', 'EFA14A', 1, 5),
	('Pastel orange ', 'E7722F', 1, 6),
	('Orange ', 'E45D2C', 1, 7),
	('Red ', 'C20034', 1, 8),
	('Purple ', '9B0F33', 1, 9),
	('Light pink ', 'EF7490', 1, 10),
	('Pink ', 'D01A74', 1, 11),
	('Lilac ', 'D0A2C8', 1, 12),
	('Lavender ', '8766A6', 1, 13),
	('Violet ', '692C89', 1, 14),
	('Gentian ', '23529E', 1, 15),
	('Royal blue ', '333181', 1, 16),
	('Light blue ', '7EACD8', 1, 17),
	('Mint ', '81D1CF', 1, 18),
	('Turquoise ', '1DB4AB', 1, 19),
	('Turquoise blue ', '1B96A8', 1, 20),
	('Green ', '2BA633', 1, 21),
	('Green ', '13874E', 1, 22),
	('Dark green ', '264F2B', 1, 23),
	('Sage ', '8B8D59', 1, 24),
	('Beige ', 'E6DBBF', 1, 25),
	('Tan ', 'AE7735', 1, 26),
	('Mustard ', 'EEB673', 1, 27),
	('Hazelnut ', 'BD502C', 1, 28),
	('Brown ', '593A2B', 1, 29),
	('Gray ', '919287', 1, 30),
	('Black ', '000000', 1, 31),
	('Dark gray ', '575C61', 1, 32),
	('Silver ', 'A4A1AD', 1, 33),
	('Gold ', 'AB9C6B', 1, 34),
	('Copper', '9E7B52', 1, 35);
");
$installer->endSetup(); 
