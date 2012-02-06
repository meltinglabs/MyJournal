<?php
/**
 * @package myjournal
 */
$xpdo_meta_map['MyArticle']= array (
  'package' => 'myjournal',
  'version' => '1.1',
  'aggregates' => 
  array (
    'Container' => 
    array (
      'class' => 'MyJournal',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
