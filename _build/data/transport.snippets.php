<?php
/**
* @package myjournal
* @subpackage build
*/
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 0,
    'name' => 'MyJournal',
    'description' => 'List articles in the current container',
    'snippet' => getSnippetContent($sources['snippets'], 'snippet.myarticles_dev'),
),'',true,true);
// $properties = include $sources['build'].'properties/properties.cliche.php';
// $snippets[0]->setProperties($properties);
// unset($properties, $content);

return $snippets;