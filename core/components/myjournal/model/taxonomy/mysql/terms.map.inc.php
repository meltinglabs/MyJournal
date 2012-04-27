<?php
/**
 * @package taxonomy
 */
$xpdo_meta_map['Terms']= array (
  'package' => 'taxonomy',
  'version' => '1.1',
  'table' => 'terms',
  'fields' => 
  array (
    'value' => '',
    'alias' => '',
  ),
  'fieldMeta' => 
  array (
    'value' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '200',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'alias' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '200',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'unique',
    ),
  ),
  'indexes' => 
  array (
    'value' => 
    array (
      'alias' => 'value',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'value' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'alias' => 
    array (
      'alias' => 'alias',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'alias' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Attached' => 
    array (
      'class' => 'TermRelationships',
      'local' => 'id',
      'foreign' => 'term_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'TermTaxonomy' => 
    array (
      'class' => 'TermTaxonomy',
      'local' => 'id',
      'foreign' => 'term_id',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
);
