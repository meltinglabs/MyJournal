<?php
/**
 * @package taxonomy
 */
$xpdo_meta_map['TermTaxonomy']= array (
  'package' => 'taxonomy',
  'version' => '1.1',
  'table' => 'term_taxonomy',
  'fields' => 
  array (
    'term_id' => 0,
    'type' => '',
    'description' => NULL,
    'parent' => 0,
    'count' => 0,
  ),
  'fieldMeta' => 
  array (
    'term_id' => 
    array (
      'dbtype' => 'bigint',
      'precision' => '20',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'type' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '32',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'description' => 
    array (
      'dbtype' => 'longtext',
      'phptype' => 'string',
      'null' => false,
    ),
    'parent' => 
    array (
      'dbtype' => 'bigint',
      'precision' => '20',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'count' => 
    array (
      'dbtype' => 'bigint',
      'precision' => '20',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'term_id' => 
    array (
      'alias' => 'term_id',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'term_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'type' => 
    array (
      'alias' => 'type',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'type' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Term' => 
    array (
      'class' => 'Terms',
      'key' => 'id',
      'local' => 'term_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
  'composites' => 
  array (
    'Relationships' => 
    array (
      'class' => 'TermRelationships',
      'local' => 'term_id',
      'foreign' => 'term_id',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
