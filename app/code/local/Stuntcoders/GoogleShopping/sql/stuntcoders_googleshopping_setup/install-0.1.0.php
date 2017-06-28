<?php

$installer = $this;

$installer->startSetup();

$installer->getConnection()->dropTable($installer->getTable('stuntcoders_googleshopping/feed'));

$table = $installer->getConnection()
    ->newTable($installer->getTable('stuntcoders_googleshopping/feed'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Feed id')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => true,
        'default'   => '',
    ), 'Feed path')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => true,
        'default'   => '',
    ), 'Feed title')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
        'default'   => '',
    ), 'Feed description');

$installer->getConnection()->createTable($table);

$installer->endSetup();
