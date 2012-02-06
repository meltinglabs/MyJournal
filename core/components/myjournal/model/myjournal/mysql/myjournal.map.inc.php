<?php
/**
 * @package myjournal
 */
$xpdo_meta_map['MyJournal']= array (
  'package' => 'myjournal',
  'version' => '1.1',
  'composites' => 
  array (
    'Articles' => 
    array (
      'class' => 'MyArticle',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
