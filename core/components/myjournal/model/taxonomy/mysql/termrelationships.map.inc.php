<?php
/**
 * @package taxonomy
 */
$xpdo_meta_map['TermRelationships']= array (
  'package' => 'taxonomy',
  'version' => '1.1',
  'table' => 'term_relationships',
  'fields' => 
  array (
    'owner_id' => 0,
    'term_id' => 0,
    'rank' => 0,
  ),
  'fieldMeta' => 
  array (
    'owner_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'pk',
    ),
    'term_id' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'attributes' => 'unsigned',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'index' => 'pk',
    ),
    'rank' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => true,
      'type' => 'BTREE',
      'columns' => 
      array (
        'owner_id' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'term_id' => 
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
    'Taxonomy' => 
    array (
      'class' => 'TermTaxonomy',
      'key' => 'id',
      'local' => 'owner_id',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
