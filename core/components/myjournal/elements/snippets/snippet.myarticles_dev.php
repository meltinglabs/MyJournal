<?php
/**
 * Snippet MyJournal
 *
 * @package myjournal
 */
 /**
 * MyJournal
 *
 * A Gallery manager components for MODx Revolution
 *
 * @author Stephane Boulard <lossendae@gmail.com>
 * @package myjournal
 */
 $MyJournal = $modx->getService('myjournal','MyJournal',$modx->getOption('myjournal.core_path',null,$modx->getOption('core_path').'components/myjournal/').'model/myjournal/',$scriptProperties);
if (!($MyJournal instanceof MyJournal)) return 'MyJournal could not be loaded';
$view = $modx->getOption('view', $_REQUEST, $modx->getOption('view', $scriptProperties, null));
$scriptProperties['plugin'] = $modx->getOption('plugin', $scriptProperties, 'default');
$controllerName = ($view != null) ?  ucfirst($view) : 'Albums';
$scriptProperties['view'] = strtolower($controllerName);

$controller = $MyJournal->loadController($controllerName);
$output = $controller->run($scriptProperties);
return $output;