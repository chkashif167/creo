<?php


$this->startSetup();

$table = new Varien_Db_Ddl_Table();

$table->setName($this->getTable('progos_creomob/configuration'));

$table->addColumn(
	'entity_id',
	Varien_Db_Ddl_Table::TYPE_INTEGER,
	10,
	array(
			'auto_increment' => true,
			'unsigned' => true,
			'nullable' => false,
			'primary' => true
		)
);

$table->addColumn(
    'created_at',
    Varien_Db_Ddl_Table::TYPE_DATETIME,
    null,
    array(
        'nullable' => false,
    )
);
$table->addColumn(
    'updated_at',
    Varien_Db_Ddl_Table::TYPE_DATETIME,
    null,
    array(
        'nullable' => false,
    )
);

$table->addColumn(
    'site_url',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    255,
    array(
        'nullable' => false,
    )
);

$table->addColumn(
    'api_user',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    50,
    array(
        'nullable' => false,
    )
);

$table->addColumn(
    'api_key',
    Varien_Db_Ddl_Table::TYPE_VARCHAR,
    50,
    array(
        'nullable' => false,
    )
);

$table->setOption('type', 'InnoDB');
$table->setOption('charset', 'utf8');

$this->getConnection()->createTable($table);
$this->endSetup();