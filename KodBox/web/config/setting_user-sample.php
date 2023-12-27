<?php 
$config['database'] = array (
  'DB_TYPE' => 'mysqli',
  'DB_HOST' => 'localhost',
  'DB_PORT' => 3306,
  'DB_USER' => 'KDB_USER',
  'DB_PWD' => 'KDB_PWD',
  'DB_NAME' => 'KDB_NAME',
  'DB_SQL_LOG' => true,
  'DB_FIELDS_CACHE' => true,
  'DB_SQL_BUILD_CACHE' => false,
);
$config['cache']['sessionType'] = 'file';
$config['cache']['cacheType'] = 'file';