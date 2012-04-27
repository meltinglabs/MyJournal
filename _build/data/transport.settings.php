<?php
/**
 * Loads system settings
 *
 * @package myjournal
 * @subpackage build
 */
$settings = array();

$settings['myjournal.debug_tag_call']= $modx->newObject('modSystemSetting');
$settings['myjournal.debug_tag_call']->fromArray(array(
    'key' => 'myjournal.debug_tag_call',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'myjournal',
    'area' => 'Administration',
),'',true,true);

/*
$settings['myjournal.']= $modx->newObject('modSystemSetting');
$settings['myjournal.']->fromArray(array(
    'key' => 'myjournal.',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'myjournal',
    'area' => '',
),'',true,true);
*/

return $settings;