<?php
$xpdo_meta_map['OptiPicImage']= array (
  'package' => 'optipic',
  'version' => '1.1',
  'table' => 'optipic_images',
  'extends' => 'xPDOSimpleObject',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'file' => '',
    'optimized' => '',
    'indexed' => 0,
  ),
  'fieldMeta' => 
  array (
    'file' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'text',
      'null' => true,
      'default' => '',
    ),
    'optimized' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'text',
      'null' => true,
      'default' => '',
    ),
    'indexed' => 
    array (
      'dbtype' => 'tinyint',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
  ),
);
